<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Entrust;
use App\Models\User;
use App\Models\Role;

class VerifyWriteAuthority {

    private $methods = array('POST', 'PUT', 'PATCH', 'DELETE');

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 用户必须是admin，才能够执行增加、删除等写数据的操作（除了调用api/sessions, api/updateUserProfile）
        $valid = true;
        $mustCheck = !($request->is('*api/sessions*')) && !($request->is('*api/updateUserProfile*'));
        if ($mustCheck) {
            $method = strtoupper($request->method());
            if (in_array($method, $this->methods)) {
                if ($this->auth->check()) {
                    if (!Entrust::hasRole('admin') && !Entrust::hasRole('owner'))
                    {
                        $valid = false;
                    }

                    // admin不能修改admin跟owner的用户跟角色，只有owner可以
                    if (Entrust::hasRole('admin') && !Entrust::hasRole('owner')) {
                        if ($request->is('*admin/users*')) {
                            $valid = $this->_checkUser($request);
                        }
                        if ($request->is('*admin/roles*')) {
                            $valid = $this->_checkRole($request);
                        }
                    }

                    // owner才能修改permission与codec
                    if ($request->is('*admin/permissions*') || $request->is('*admin/codecs*')) {
                        if (!Entrust::hasRole('owner')) {
                            $valid = false;
                        }
                    }
                } else {
                    $valid = false;
                }
            }
        }

        if ($valid) {
            return $next($request);
        } else {
            return response(array(
                'error' => array(
                    'message' => '权限非法!',
                )
            ), 403);
        }
    }

    private  $names = ['admin', 'owner'];

    private function _checkUser(\Illuminate\Http\Request $request) {
        $result = true;
        $id = $this->_getId($request);
        if ($id) {
            $user = User::find($id);
            if ($user && in_array($user->name, $this->names)) {
                $result = false;
            }
        }
        return $result;
    }

    private function _checkRole(\Illuminate\Http\Request $request) {
        $result = true;
        $id = $this->_getId($request);
        if ($id) {
            $role = Role::find($id);
            if ($role && in_array($role->name, $this->names)) {
                $result = false;
            }
        }
        return $result;
    }

    private function _getId(\Illuminate\Http\Request $request) {
        $id = '';
        $segments = $request->segments();
        $lastSeg = end($segments);
        if (is_numeric($lastSeg)) {
            $id = intval($lastSeg);
        }
        return $id;
    }
}
