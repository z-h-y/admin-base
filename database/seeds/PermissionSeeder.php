<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Codec;

class PermissionSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = array(
            array('name' => 'system:resource:dashboard', 'display_name' => '系统:资源:仪表板'),
            array('name' => 'system:resource:admin-user', 'display_name' => '系统:资源:后台-用户'),
            array('name' => 'system:resource:admin-role', 'display_name' => '系统:资源:后台-角色'),
        );

        $permissionsOwner = array(
            array('name' => 'system:resource:admin-permission', 'display_name' => '系统:资源:后台-权限'),
            array('name' => 'system:resource:admin-codec', 'display_name' => '系统:资源:后台-编码'),
        );

        foreach ($permissions as $data) {
            $name = $data['name'];
            $permission = Permission::where('name', '=', $name)->first();
            if (!$permission) {
                Permission::create($data);
            }
        }

        foreach ($permissionsOwner as $data) {
            $name = $data['name'];
            $permission = Permission::where('name', '=', $name)->first();
            if (!$permission) {
                Permission::create($data);
            }
        }

        // basic user role must have dashboard permission
        $dashPer = Permission::where('name', '=', 'system:resource:dashboard')->first();
        $basicRole = Role::where('name', '=', 'user')->first();
        $basicRole->perms()->sync(array($dashPer->id));

        // admin role must have all permissions except owner permissions
        $adminRole = Role::where('name', '=', 'admin')->first();
        $allPermissions = Permission::all();
        $ids = array();
        foreach ($allPermissions as $permission) {
            $permissionName = $permission['name'];
            if (($permissionName != $permissionsOwner[0]['name']) && ($permissionName != $permissionsOwner[1]['name'])) {
                array_push($ids, $permission->id);
            }
        }
        $adminRole->perms()->sync($ids);

        // owner role must have all permissions
        $ownerRole = Role::where('name', '=', 'owner')->first();
        $ids = array();
        foreach ($allPermissions as $permission) {
            array_push($ids, $permission->id);
        }
        $ownerRole->perms()->sync($ids);

    }

}
