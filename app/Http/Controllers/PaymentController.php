<?php

namespace App\Http\Controllers;

use App\Helpers\NumberHelper;
use App\Helpers\ResponseHelper;
use App\Models\Payment;
use App\Models\PaymentDetails;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function snapGenerate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'month' => 'required|integer',
            'sale_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(ResponseHelper::warning( errors: $validator->errors(), code: 404), 404);
        }

        $sale_id = $request->sale_id;
        $month = $request->month;
        $qry = Sale::query()->select("id", "grand_total");
        $data = $qry->whereId($sale_id)->first();

        if (!$data) {
            return response()->json(ResponseHelper::warning( message: 'Data Not Found', code: 404), 404);
        }

        DB::beginTransaction();
        try {



            $order_id = NumberHelper::generateNumber("PYM");
            $credits = NumberHelper::generateTableCredit($data->grand_total, $month);

            $payment = Payment::create([
                'user_id' => 5,
                'sale_id' => $sale_id,
                'order_id' => $order_id,
                'periode' => $month,
            ]);

            $unique_id_midtrans = null;
            $gross_amount = 0;
            $payment_detail_id = null;
            // $res = [];
            foreach ($credits[$month] as $key => $credit) {
                $unique_id = NumberHelper::generateNumber("PYD");
                $instalment = $credit['price'];
                $m = $credit['month'];
                // $res[] = $unique_id;


                $first_date_of_payment = Carbon::now()->addMonths($m)->startOfMonth()->format('Y-m-d');
                $last_date_of_payment = Carbon::now()->addMonths($m)->setDays(10)->format('Y-m-d');

                $payment_detail = $payment->payment_details()->create([
                    'unique_id' => $unique_id,
                    'instalment' => $instalment,
                    'first_date_of_payment' => $first_date_of_payment,
                    'last_date_of_payment' => $last_date_of_payment,
                    'snap_token' => 'abc',
                ]);

                if ($key == 0) {
                    $unique_id_midtrans = $unique_id;
                    $gross_amount = $instalment;
                    $payment_detail_id = $payment_detail->id;
                }
            }

            \Midtrans\Config::$serverKey = config('midtrans.midtrans_server_key');
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;


            $user = auth()->user();

            $params = array(
                'transaction_details' => array(
                    'order_id' => $unique_id_midtrans,
                    'gross_amount' => $gross_amount,
                ),
                'customer_details' => array(
                    'first_name' => $user->name,
                    'email' => $user->email,
                ),
            );

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            DB::commit();
            return response()->json(ResponseHelper::success(data: [
                'token' => $snapToken,
                'payment_detail_id' => $payment_detail_id,
            ]), 200);

        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json(ResponseHelper::error(th: $th), 500);
        }


    }

    public function pay(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'payment_detail_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(ResponseHelper::warning( errors: $validator->errors(), code: 404), 404);
        }

        try {

            $payment_detail_id = $request->payment_detail_id;
            $payment_detail = PaymentDetails::whereId($payment_detail_id)->first();
            if (!$payment_detail) {
                return response()->json(ResponseHelper::warning( message: 'Data Not Found', code: 404), 404);
            }

            $payment_detail->update([
                'status' => 'PAID',
                'payment_date' => now()
            ]);

            DB::commit();
            return response()->json(ResponseHelper::success(), 200);

        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json(ResponseHelper::error(th: $th), 500);
        }


    }

    
}
