<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Helpers\DatatableHelper;
use App\Helpers\FileHelper;
use App\Helpers\ResponseHelper;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function datatables(Request $request)
    {
        $model = new User();
        $table = $model->getTable();
        $columns  = Schema::getColumnListing($table);

		$search = $request->get("search");
        $per_page = $request->get('per_page', 10);
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
        $data = $qry->paginate($per_page);
        try {
            return response()->json(ResponseHelper::success(data: $data), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);

        }
    }
}
