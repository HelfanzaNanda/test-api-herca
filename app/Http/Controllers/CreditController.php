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

class CreditController extends Controller
{
    public function datatables(Request $request)
    {
        $model = new Payment();
        $table = $model->getTable();
        $columns  = Schema::getColumnListing($table);

		$search = $request->get("search");
        $per_page = $request->get('per_page', 10);
        $qry = Payment::query()->where("user_id", auth()->id());
        $qry = $qry->with("sale");
        $qry = $qry->withCount(['payment_details' => function ($q){
            $q->where("status", "PAID");
        }], 'id');
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
