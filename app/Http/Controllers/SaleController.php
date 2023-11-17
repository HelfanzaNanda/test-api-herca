<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Helpers\DatatableHelper;
use App\Helpers\FileHelper;
use App\Helpers\NumberHelper;
use App\Helpers\ResponseHelper;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'price' => 'required|numeric',
    //         'status' => 'required',
    //         'filecode' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(ResponseHelper::warning( validations: $validator->errors(), code: 422), 422);
    //     }

    //     try {
    //         $params = $validator->validated();
    //         $params['file_id'] = FileHelper::searchFilecode($params['filecode']);
    //         unset($params['filecode']);
    //         Product::create($params);
    //         return response()->json(ResponseHelper::success(), 200);

    //     } catch (\Throwable $th) {
    //         return response()->json(ResponseHelper::error(th: $th), 500);
    //     }
    // }


    // public function find($id)
    // {
    //     $product = Product::whereId($id)->with('file')->first();
    //     if (!$product) {
    //         return response()->json(ResponseHelper::warning( message: 'product not found', code: 404), 404);
    //     }
    //     return response()->json(ResponseHelper::success(data: $product), 200);
    // }

    // public function update($id, Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'price' => 'required|numeric',
    //         'status' => 'required',
    //         'filecode' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(ResponseHelper::warning( validations: $validator->errors(), code: 422), 422);
    //     }

    //     $product = Product::find($id);
    //     if (!$product) {
    //         return response()->json(ResponseHelper::warning( message: 'product not found', code: 404), 404);
    //     }


    //     try {
    //         $params = $validator->validated();
    //         $params['file_id'] = FileHelper::searchFilecode($params['filecode']);
    //         unset($params['filecode']);
    //         $product->update($params);
    //         return response()->json(ResponseHelper::success(), 200);

    //     } catch (\Throwable $th) {
    //         return response()->json(ResponseHelper::error(th: $th), 500);
    //     }
    // }

    // public function delete($id)
    // {
    //     $product = Product::whereId($id)->with('file')->first();
    //     if (!$product) {
    //         return response()->json(ResponseHelper::warning( message: 'product not found', code: 404), 404);
    //     }

    //     try {
    //         $product->file->delete();
    //         $product->delete();
    //         return response()->json(ResponseHelper::success(), 200);
    //     } catch (\Throwable $th) {
    //         return response()->json(ResponseHelper::error(th: $th), 500);
    //     }
    // }

    public function datatables(Request $request)
    {
        $model = new Sale();
        $table = $model->getTable();
        $columns  = Schema::getColumnListing($table);

		$search = $request->get("search");
        $per_page = $request->get('per_page', 10);
        $qry = Sale::query()->with('user');
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

    public function credit($sale_id)
    {

        if (!$sale_id) {
            return response()->json(ResponseHelper::warning( message: 'Data Not Found', code: 404), 404);
        }

        $qry = Sale::query()->select("id", "grand_total");
        $data = $qry->whereId($sale_id)->first();

        if (!$data) {
            return response()->json(ResponseHelper::warning( message: 'Data Not Found', code: 404), 404);
        }

        $credits = NumberHelper::generateTableCredit($data->grand_total);

        // $periode = [
        //     [ "month" => 6, "bank_interest" => 5, ],
        //     [ "month" => 9, "bank_interest" => 7, ],
        //     [ "month" => 12, "bank_interest" => 8, ],
        // ];

        // $res = [];

        // foreach ($periode as $value) {
        //     $credit = [];
        //     $month = $value['month'];
        //     $bank_interest = $value['bank_interest'];
        //     $credit[$month] = [];
        //     $price = ($data->grand_total / $month) * $bank_interest;
        //     for ($i=1; $i <= $month; $i++) {
        //         $credit[$month][] = [
        //             'month' => $i,
        //             'price' => $price
        //         ];
        //     }
        //     // array_push($res, $credit);
        //     $res[] = $credit;
        // }

        // foreach ($data as $item) {

        // }



        try {
            return response()->json(ResponseHelper::success(data: $credits), 200);

        } catch (\Throwable $th) {
            return response()->json(ResponseHelper::error(th: $th), 500);

        }
    }






}
