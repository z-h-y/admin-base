<?php namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's global HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
		'Illuminate\Session\Middleware\StartSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
		'App\Http\Middleware\VerifyCsrfToken',
		'App\Http\Middleware\VerifyWriteAuthority',
        'App\Http\Middleware\DataFormatFilter',
	];

	/**
	 * The application's route middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
        'filter.dateRange' => 'App\Http\Middleware\DateRangeFilter',
		'auth.api' => 'App\Http\Middleware\Auth\ApiAuthenticate',
		'auth.admin' => 'App\Http\Middleware\Auth\AdminAuthenticate',
		'auth.owner' => 'App\Http\Middleware\Auth\OwnerAuthenticate',
		'auth.action' => 'App\Http\Middleware\Auth\ActionPermissionAuthenticate',
	];

}
