<?php namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class ApiAuthenticate {

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
        // User must login first to request any api endpoints unless the request url contains 'api/sessions' (login/logout)
        // 本过滤器要求用户必须登录（除了调用api/sessions）
        if (!$request->is('*api/sessions*') && $this->auth->guest())
        {
            return response(array(
                'error' => array(
                    'message' => 'Please login first!',
                )
            ), 401);
        }

        return $next($request);
    }

}
