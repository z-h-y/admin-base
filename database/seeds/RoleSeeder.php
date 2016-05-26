<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = array(
            array('name' => 'owner', 'display_name' => 'owner'),
            array('name' => 'admin', 'display_name' => '管理员'),
            array('name' => 'user', 'display_name' => '普通用户'),
        );

        foreach ($roles as $data) {
            $name = $data['name'];
            $role = Role::where('name', '=', $name)->first();
            if (!$role) {
                Role::create($data);
            }
        }

        // all user must have the user role at least
        $basicRole = Role::where('name', '=', 'user')->first();
        $allUsers = User::where('name', '!=', 'admin')->get();
        foreach ($allUsers as $user) {
            $userName = $user['name'];
            if ($userName != 'admin' && $userName != 'owner') {
                if (!$user->hasRole($basicRole->name)) {
                    $user->attachRole($basicRole);
                }
            }
        }

        // admin user must have admin role
        $adminRole = Role::where('name', '=', 'admin')->first();
        $adminUser = User::where('name', '=', 'admin')->first();
        if (!$adminUser->hasRole($adminRole->name)) {
            $adminUser->attachRole($adminRole);
        }

        // owner user must have owner role
        $ownerRole = Role::where('name', '=', 'owner')->first();
        $ownerUser = User::where('name', '=', 'owner')->first();
        if (!$ownerUser->hasRole($ownerRole->name)) {
            $ownerUser->attachRole($ownerRole);
        }
    }

}
