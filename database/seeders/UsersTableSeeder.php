<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Super User',
                'email' => 'superamb@gmail.com',
                'level' => '0',
                'password' => bcrypt('superk155my455'),
            ],
            [
                'name' => 'System User',
                'email' => 'systemamb@gmail.com',
                'level' => '1',
                'password' => bcrypt('sy5temAcc355'),
            ],
            [
                'name' => 'Inventory User',
                'email' => 'inventoryamb@gmail.com',
                'level' => '2',
                'password' => bcrypt('1nvent0ryAcc355'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
