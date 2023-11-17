<?php
namespace App\Helpers;

use App\Models\Payment;
use App\Models\PaymentDetails;
use Carbon\Carbon;
use Exception;

class NumberHelper {

    public static function calculateCommision($revenue = 0)
    {
        if ($revenue >= 500000000) {
            return 0.1;
        }elseif ($revenue >= 200000000 && $revenue < 500000000) {
            return 0.05;
        }elseif ($revenue >= 100000000 && $revenue < 200000000) {
            return 0.025;
        }else {
            return 0;
        }
    }

    public static function generateNumber($prefix)
    {
        if (!$prefix) {
            new Exception("Prefix is Required");
        }

        $now = Carbon::now();
        $month = $now->format('m');
        $year = $now->format('Y');

        switch ($prefix) {
            case 'PYM':
                    $id = 1;
                    $payment = Payment::query()
                        ->whereYear("created_at", $year)
                        ->whereMonth("created_at", $month)
                        ->latest("id")
                        ->first();
                    if ($payment) {
                        $order_id = $payment->order_id;
                        $ids = explode('-', $order_id);
                        $id = $ids[count($ids) - 1];
                        $id += 1;
                    }

                    $prefix_date = $month . $year;
                    $order_id = $prefix .'-'. $prefix_date .'-'. sprintf('%04d', $id);

                    return $order_id;

                break;
            case 'PYD':
                    $id = 1;
                    $data = PaymentDetails::query()
                        ->whereYear("created_at", $year)
                        ->whereMonth("created_at", $month)
                        ->latest("id")->first();
                        if ($data) {
                            $unique_id = $data->unique_id;
                            $ids = explode('-', $unique_id);
                            $id = $ids[count($ids) - 1];
                            $id += 1;
                            // return $id;
                    }

                    $prefix_date = $month . $year;
                    $unique_id = $prefix .'-'. $prefix_date .'-'. sprintf('%04d', $id);

                    return $unique_id;
                break;

            default:
                new Exception("Prefix is Required");
                break;
        }
    }

    public static function generateTableCredit($grand_total, $month = null)
    {

        $periods = [
            [ "month" => 6, "bank_interest" => 5, ],
            [ "month" => 9, "bank_interest" => 7, ],
            [ "month" => 12, "bank_interest" => 8, ],
        ];

        if ($month) {

            $found_key = array_search($month, array_column($periods, 'fav_color'));
            $value = $periods[$found_key];

            // $res = [];
            $credit = [];
            $month = $value['month'];
            $bank_interest = $value['bank_interest'];
            $credit[$month] = [];
            $price = ($grand_total / $month) * $bank_interest;
            for ($i=1; $i <= $month; $i++) {
                $credit[$month][] = [
                    'month' => $i,
                    'price' => ceil($price)
                ];
            }
            // array_push($res, $credit);
            // $res[] = $credit;

            // foreach ($periode as $value) {
            // }

            return $credit;
        }



        $res = [];

        foreach ($periods as $value) {
            $credit = [];
            $month = $value['month'];
            $bank_interest = $value['bank_interest'];
            $credit[$month] = [];
            $price = ($grand_total / $month) * $bank_interest;
            for ($i=1; $i <= $month; $i++) {
                $credit[$month][] = [
                    'month' => $i,
                    'price' => ceil($price)
                ];
            }
            // array_push($res, $credit);
            $res[] = $credit;
        }

        return $res;
    }
}
