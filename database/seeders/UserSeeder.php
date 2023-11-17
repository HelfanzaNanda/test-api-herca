<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            'alfandy', 'mery', 'danang', 'admin', 'customer'
        ];

        foreach ($users as $user) {
            $modelUser = User::create([
                'name' => $user,
                'email' => $user . '@example.com',
                'password' => Hash::make('password')
            ]);

            if ($user == 'admin') {
                $modelUser->assignRole('ADMIN');
            }else if ($user == 'customer') {
                $modelUser->assignRole('CUSTOMER');
            }else{
                $modelUser->assignRole('MARKETING');
            }
        }
    }
}
