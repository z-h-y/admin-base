<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\Renew',
		'App\Console\Commands\MigratePermissionType',
        'App\Console\Commands\UpdateOwnerPassword',
        'App\Console\Commands\BackupLeanCloud',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		// 定时将leancloud的数据同步到本地mongo
		$backupInterval = intval(config('leancloud.backup_interval')); // 同步时间间隔
		if ($backupInterval !== 0) {
			if ($backupInterval <= 0 || $backupInterval > 24) {
				$defaultInterval = intval(config('leancloud.backup_default_interval')); // 默认同步时间间隔
				$backupInterval = $defaultInterval;
			}
			$schedule->command('admin-base:backup-leancloud')->cron('0 ' . '*/' . $backupInterval . ' * * *')->withoutOverlapping();
		}
	}

}
