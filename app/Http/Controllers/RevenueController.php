<?php

namespace App\Http\Controllers;

use App\Helpers\DatatableHelper;
use App\Helpers\NumberHelper;
use App\Helpers\PaginateHelper;
use App\Helpers\ResponseHelper;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class RevenueController extends Controller
{
    public function datatables(Request $request)
    {
        $model = new User();
        $table = $model->getTable();
        $columns  = Schema::getColumnListing($table);

        // $data = Sale::query()->select("id", "user_id", "date", "total_balance")
        //     ->with('user:id,name')->get();


        $search = $request->get("search");
        $per_page = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $qry = User::query();
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
        $qry = $qry->select("id", "name");
        $qry->with('sales:id,user_id,date,total_balance');
        $users = $qry->get();

        $data = [];
        $id = 1;
        foreach ($users as $user) {
            $sales = $user->sales->groupBy(function($item, $key) {
                return Carbon::parse($item->date)->format('F');
            });
            foreach ($sales as $month => $sale) {
                $revenue = $sale->sum("total_balance");
                $commission_percentage = NumberHelper::calculateCommision($revenue);
                $item = [
                    'id' => $id,
                    'user_name' => $user->name,
                    'month' => $month,
                    // 'sales' => $sales,
                    'revenue' => $revenue,
                    'commission_percentage' => $commission_percentage * 100 . '%',
                    'commission_nominal' => $revenue * $commission_percentage,
                ];
                $id++;

                array_push($data, $item);
            }
        }
        $data = PaginateHelper::paginate($data,$per_page);

        return response()->json(ResponseHelper::success(data: $data), 200);




        try {
            $data = DatatableHelper::make($cmd, $columns, $start, $length, $order, $search);
            return response()->json(ResponseHelper::success(data: $data), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);

        }
    }
}
