<?php

namespace Database\Seeders;

use App\Models\Sale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = '[
            { "transaction_number": "TRX001", "user_id": 1, "date": "2023-05-22", "cargo_fee": 25000, "total_balance": 3000000, "grand_total": 3025000 },
            { "transaction_number": "TRX002", "user_id": 3, "date": "2023-05-22", "cargo_fee": 25000, "total_balance": 320000, "grand_total": 345000 },
            { "transaction_number": "TRX003", "user_id": 1, "date": "2023-05-22", "cargo_fee": 0, "total_balance": 65000000, "grand_total": 65000000 },
            { "transaction_number": "TRX004", "user_id": 1, "date": "2023-05-23", "cargo_fee": 10000, "total_balance": 70000000, "grand_total": 70010000 },
            { "transaction_number": "TRX005", "user_id": 2, "date": "2023-05-23", "cargo_fee": 10000, "total_balance": 80000000, "grand_total": 80010000 },
            { "transaction_number": "TRX006", "user_id": 3, "date": "2023-05-23", "cargo_fee": 12000, "total_balance": 44000000, "grand_total": 44012000 },
            { "transaction_number": "TRX007", "user_id": 1, "date": "2023-06-01", "cargo_fee": 0, "total_balance": 75000000, "grand_total": 75000000 },
            { "transaction_number": "TRX008", "user_id": 2, "date": "2023-06-02", "cargo_fee": 0, "total_balance": 85000000, "grand_total": 85000000 },
            { "transaction_number": "TRX009", "user_id": 2, "date": "2023-06-01", "cargo_fee": 0, "total_balance": 175000000, "grand_total": 175000000 },
            { "transaction_number": "TRX010", "user_id": 3, "date": "2023-06-01", "cargo_fee": 0, "total_balance": 75000000, "grand_total": 75000000 },
            { "transaction_number": "TRX011", "user_id": 2, "date": "2023-06-01", "cargo_fee": 0, "total_balance": 750020000, "grand_total": 750020000 },
            { "transaction_number": "TRX012", "user_id": 3, "date": "2023-06-01", "cargo_fee": 0, "total_balance": 130000000, "grand_total": 120000000 }
        ]';

        $arr = json_decode($json, true);

        foreach ($arr as $item) {
            // dd($item['transaction_number']);
            Sale::create($item);
        }
    }
}
