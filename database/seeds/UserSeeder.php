<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = array(
            array('email' => 'owner@test.com', 'name' => 'owner', 'password' => Hash::make('er87kj1p6hfc23')),
            array('email' => 'admin@test.com', 'name' => 'admin', 'password' => Hash::make('rhj35g6e27')),
            array('email' => 'test@test.com', 'name' => 'test', 'password' => Hash::make('jk12rtyn78')),
        );

        foreach ($users as $data) {
            $email = $data['email'];
            $name = $data['name'];
            $user = User::where('email', '=', $email)->first();
            $user = $user or User::where('name', '=', $name)->first();
            if (!$user) {
                User::create($data);
            }
        }
    }

}
