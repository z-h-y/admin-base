<?php namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Entrust;

class OwnerAuthenticate {

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
        // User must have owner role
        // 本过滤器要求用户必须具有owner角色
        if ($this->auth->check()) {
            if (!Entrust::hasRole('owner') )
            {
                return response(array(
                    'error' => array(
                        'message' => 'You are not owner!',
                    )
                ), 403);
            }
        } else {
            return response(array(
                'error' => array(
                    'message' => 'Please login first!',
                )
            ), 401);
        }

        return $next($request);
    }

}
