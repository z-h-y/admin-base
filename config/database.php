<?php

return [

	/*
	|--------------------------------------------------------------------------
	| PDO Fetch Style
	|--------------------------------------------------------------------------
	|
	| By default, database results will be returned as instances of the PHP
	| stdClass object; however, you may desire to retrieve records in an
	| array format for simplicity. Here you can tweak the fetch style.
	|
	*/

	'fetch' => PDO::FETCH_ASSOC,

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work. Of course
	| you may use many connections at once using the Database library.
	|
	*/

	'default' => 'mysql',

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => [

		'mysql' => [
			'driver'    => 'mysql',
			'host'      => env('DB_MYSQL_HOST', 'localhost'),
			'database'  => env('DB_MYSQL_DATABASE', ''),
			'username'  => env('DB_MYSQL_USERNAME', ''),
			'password'  => env('DB_MYSQL_PASSWORD', ''),
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
			'strict'    => false,
		],

		'mongodb' => [
			'driver'   => 'mongodb',
			'host'     => env('DB_MONGO_HOST', 'localhost'),
			'port'     => env('DB_MONGO_PORT', 27017),
			'username' => env('DB_MONGO_USERNAME', ''),
			'password' => env('DB_MONGO_PASSWORD', ''),
			'database' => env('DB_MONGO_DATABASE', ''),
		],

        'leancloud_backup' => [
            'driver'   => 'mongodb',
            'host'     => env('DB_LC_BACKUP_HOST', 'localhost'),
            'port'     => env('DB_LC_BACKUP_PORT', 27017),
            'username' => env('DB_LC_BACKUP_USERNAME', ''),
            'password' => env('DB_LC_BACKUP_PASSWORD', ''),
            'database' => env('DB_LC_BACKUP_DATABASE', ''),
        ]

	],

	/*
	|--------------------------------------------------------------------------
	| Migration Repository Table
	|--------------------------------------------------------------------------
	|
	| This table keeps track of all the migrations that have already run for
	| your application. Using this information, we can determine which of
	| the migrations on disk haven't actually been run in the database.
	|
	*/

	'migrations' => 'migrations',

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/

	'redis' => [

		'cluster' => false,

		'default' => [
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		],

	],

];
