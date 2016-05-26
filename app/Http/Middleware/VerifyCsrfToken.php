<?php namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		// 如果包含规则正确(md5(date+'appgame')的header - x-ignore-csrf，则忽略csrf检测
		$token = $request->header('x-ignore-csrf');
        $ignoreToken = md5((Carbon::today()->toDateString() . 'appgame'));
		if ($token === $ignoreToken) {
			return $next($request);
		} else {
			return parent::handle($request, $next);
		}
	}

}
