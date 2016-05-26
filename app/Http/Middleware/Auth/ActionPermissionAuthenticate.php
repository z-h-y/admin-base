<?php namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Entrust;

class ActionPermissionAuthenticate {

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
    public function handle($request, Closure $next, $nameSpace, $modelName)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $flag = false;

        $list = $nameSpace . ':action' . ':list-' . $modelName;
        $edit = $nameSpace . ':action' . ':edit-' . $modelName;
        $delete = $nameSpace . ':action' . ':delete-' . $modelName;

        switch ($method) {
            case 'GET':
                if (Entrust::can($list)) {
                    $flag = true;
                }
                break;
            case 'PATCH':
            case 'POST':
            case 'PUT':
                if (Entrust::can($edit)) {
                    $flag = true;
                }
                break;
            case 'DELETE':
                if (Entrust::can($delete)) {
                    $flag = true;
                }
                break;
        }

        if (!$flag) {
            return response(array(
                'error' => array(
                    'message' => '你的权限不足!请联系管理员',
                )
            ), 403);
        }
        $response = $next($request);
        return $response;
    }

}
