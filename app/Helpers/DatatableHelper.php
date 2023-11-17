<?php
namespace App\Helpers;



use App\Helpers\SqlHelper;



class DatatableHelper {
	public static function make($cmd = null, $columns = [], $start, $length, $order = [], $search)
	{
		if(!$cmd) {
			throw new \Exception("cmd is required");
		}

        if (!$order) {
            $order['column'] = 'created_at';
            $order['dir'] = 'desc';
        }

        // return [
        //     'start' => $start,
        //     'length' => $length,
        //     'order' => $order,
        //     'search' => $search,
        // ];

        // return $order;



		// $columns = $request->get("columns");


        $search = strtolower($search);
		$totalData = $cmd->count();
		$totalFiltered = $cmd->count();

        // return $search;

        if (!$search) {
            if ($length > 0) {
                $cmd->skip($start)->take($length);
            }

			$cmd->latest('updated_at');
			// foreach ($order as $row) {
			// 	$cmd->orderBy($row['column'], $row['dir']);
			// }
            $cmd->orderBy($order['col'], $order['dir']);


		} else {
            $cmd->where(function($q) use($columns, $search) {
                foreach ($columns as $field) {
                    $q->orWhereRaw("lower($field) like ?", ["%{$search}%"]);
                }
            });

			$totalFiltered = $cmd->count();
			if ($length > 0) {
				$cmd->skip($start)->take($length);
			}

			$cmd->latest('updated_at');
			// foreach ($order as $row) {
			// 	$cmd->orderBy($row['column'], $row['dir']);
			// }
            $cmd->orderBy($order['col'], $order['dir']);

		}




		$rows = $cmd->get();

        $data = [
            'data' => $rows,
            // 'totalData' => $totalData,
            // 'totalFiltered' => $totalFiltered,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            // 'draw' => $draw,
        ];
        if (env('APP_DEBUG')) {
            $data['sql'] = SqlHelper::getSqlWithBindings($cmd);
        }

        return $data;

	}
}
