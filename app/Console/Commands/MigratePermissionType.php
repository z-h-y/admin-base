<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;

class MigratePermissionType extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin-base:migrate-permission-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove permission type, add namespaces.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->error('Dangerous!! Please backup your database first!!');

        if ($this->confirm('Are you already backup your database?'))
        {
            if ($this->confirm('Are you really sure run the command?'))
            {
                $permissions = array(
                    array('name' => 'dashboard', 'display_name' => '系统:资源:仪表板'),
                    array('name' => 'admin-user', 'display_name' => '系统:资源:后台-用户'),
                    array('name' => 'admin-role', 'display_name' => '系统:资源:后台-角色'),
                    array('name' => 'admin-permission', 'display_name' => '系统:资源:后台-权限'),
                    array('name' => 'admin-codec', 'display_name' => '系统:资源:后台-编码'),
                );

                $prefix = 'system:resource:';
                foreach($permissions as $permissionData) {
                    $permission = Permission::where('name', '=', $permissionData['name'])->first();
                    if ($permission) {
                        $permission->name = $prefix . $permission->name;
                        $permission->display_name = $permissionData['display_name'];
                        $permission->save();
                    }
                }

                $this->info('Finished!');
            }
        }
    }
}
