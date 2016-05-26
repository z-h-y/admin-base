<?php namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Auth;
use App\Models\OperationLog;
use Route;

class EventServiceProvider extends ServiceProvider {

    private $basicModels = array('App\Models\User', 'App\Models\Role', 'App\Models\Permission', 'App\Models\Codec');

    private $extraModels = array();

    const SESSION_DESTROY = 'sessions.destroy';

	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [];

	/**
	 * Register any other events for your application.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
		parent::boot($events);

        $this->handleModelEvent();
	}

    /**
     * 监听模型事件
     */
    private function handleModelEvent() {
        $models = array_merge($this->basicModels, $this->extraModels);
        foreach ($models as $model) {
            $model::created(function($model) {
                $this->modelHandler('created', $model);
            });
            $model::updated(function($model) {
                $this->modelHandler('updated', $model);
            });
            $model::deleted(function($model) {
                $this->modelHandler('deleted', $model);
            });
        }
    }

    /**
     * 模型发生变化时，保存操作日志
     *
     * @param $event
     * @param $model
     */
    private function modelHandler($event, $model) {
        $routeName = Route::currentRouteName();
        if (strpos($routeName, self::SESSION_DESTROY) === false) { // 当用户退出时，会更新users表的remember_token，此时不用保存操作日志
            if ($model && $event) {
                $log = new OperationLog();
                $log->event = $event;
                $log->content = json_encode($model->toArray());
                $log->table = $model->getTable();
                $log->recordId = $model->id;

                $user = Auth::user();
                if ($user) {
                    $log->username = $user->name;
                    $roles = $user->roles->toArray();
                    $roleNames = array();
                    foreach ($roles as $role) {
                        $roleNames[] = $role['name'];
                    }
                    $log->userRoles = implode(',', $roleNames);
                }
                $log->save();
            }
        }
    }
}
