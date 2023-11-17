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

class PaymentDetailController extends Controller
{
    public function snapGenerate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'payment_detail_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(ResponseHelper::warning( errors: $validator->errors(), code: 404), 404);
        }

        $payment_detail_id = $request->payment_detail_id;
        $month = $request->month;
        $qry = PaymentDetails::query()->select("id", "instalment", "unique_id");
        $data = $qry->whereId($payment_detail_id)->first();

        if (!$data) {
            return response()->json(ResponseHelper::warning( message: 'Data Not Found', code: 404), 404);
        }

        DB::beginTransaction();
        try {


            \Midtrans\Config::$serverKey = config('midtrans.midtrans_server_key');
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;


            $user = auth()->user();

            $params = array(
                'transaction_details' => array(
                    'order_id' => $data->unique_id,
                    'gross_amount' => $data->instalment,
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

    public function datatables(Request $request)
    {

        $payment_id = $request->payment_id;

        $model = new PaymentDetails();
        $table = $model->getTable();
        $columns  = Schema::getColumnListing($table);

		$search = $request->get("search");
        $per_page = $request->get('per_page', 10);
        $qry = PaymentDetails::query()->where("payment_id", $payment_id);
        if ($per_page == 'All') {
            $per_page = (int) $qry->count();
        }
        if ($search) {
            $qry->where(function($q) use($columns, $search) {
                foreach ($columns as $field) {
                    $q->orWhereRaw("lower($field) like ?", ["%{$search}%"]);
                }
            });
        }
        $data = $qry->paginate($per_page);
        try {
            return response()->json(ResponseHelper::success(data: $data), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);
        }
    }
}
