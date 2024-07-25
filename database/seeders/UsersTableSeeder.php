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
                'email' => 'superambarawa@gmail.com',
                'level' => '0',
                'password' => bcrypt('k155my455'),
            ],
            [
                'name' => 'System User',
                'email' => 'userambarawa@gmail.com',
                'level' => '1',
                'password' => bcrypt('ambarawak11my455'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
