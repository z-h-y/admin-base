<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoClient;
use GuzzleHttp\Client;

class BackupLeanCloud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin-base:backup-leancloud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
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
    public function handle()
    {
        // 读取数LeanCloud备份数据库配置
        $host = config('database.connections.leancloud_backup.host');
        $port = config('database.connections.leancloud_backup.port');
        $user = config('database.connections.leancloud_backup.username');
        $password = config('database.connections.leancloud_backup.password');
        $db = config('database.connections.leancloud_backup.database');
        $conn = 'mongodb://';
        if ($user && $password) {
            $conn .= ($password.':'.$password.'@');
        }
        $conn .= ($host.':'.$port);

        // 读取LeanCloud应用信息
        $appUrl = config('leancloud.app_url');
        $appID = config('leancloud.app_id');
        $appKey = config('leancloud.app_key', env('LEANCOULD_APP_Key'));
        $masterKey = config('leancloud.master_key', env('LEANCOULD_MASTER_Key'));
        $backupDefaultList = config('leancloud.backup_default_list'); // 默认需要同步的表
        $backupList = config('leancloud.backup_list'); // 其他需要同步的表

        if ($backupDefaultList) {
            $backupDefaultList = explode(',', $backupDefaultList);
        }
        if ($backupList) {
            $backupList = explode(',', $backupList);
        }

        if (!$backupDefaultList) {
            $backupDefaultList = [];
        }
        if ($backupList) {
            $appModels = array_merge($backupDefaultList, $backupList);
        } else {
            $appModels = $backupDefaultList;
        }

        $m = new MongoClient($conn);
        $db = $m->selectDB($db);

        // LeanCloud认证信息
        $time = time();
        $reqSign = md5($time . $masterKey) . ',' . $time . ',master';
        $headers = [
            'X-AVOSCloud-Application-Id' => $appID,
            'X-AVOSCloud-Request-Sign'=> $reqSign,
            'Content-Type' => 'application/json'
        ];

        // 同步数据
        $client = new Client(['headers' => $headers]);
        foreach ($appModels as $model) {
            $collection = $db->$model;
            $lastOne = $collection->find()->sort(array('updatedAt' => -1))->limit(1);

            if ($lastOne && $lastOne->count()) {
                $lastOne = $lastOne->getNext();
                $updatedAt = $lastOne['updatedAt'];
                $where = ['updatedAt' => ['$gte' => ['__type' => 'Date', 'iso' => $updatedAt]]];
                $response = $client->request('GET', $appUrl . '/classes/' . $model, [
                    'query' => ['where' => json_encode($where)]
                ]);
                $newRecords = $response->getBody()->getContents();
                $newRecords = json_decode($newRecords, true);

                if ($newRecords && $newRecords['results']) {
                    foreach ($newRecords['results'] as $record) {
                        $id = $record['objectId'];
                        if ($id) {
                            $record['_id'] = $record['objectId'];
                            $collection->update(array('_id' => $id), array('$set' => $record), array('upsert'=>true));
                        }
                    }
                }
            } else {
                $response = $client->request('GET', $appUrl . '/classes/' . $model);
                $result = $response->getBody()->getContents();
                if ($result) {
                    $result = json_decode($result, true);
                    if ($result && $result['results']) {
                        foreach ($result['results'] as $data) {
                            $data['_id'] = $data['objectId'];
                            $collection->insert($data);
                        }
                    }
                }
            }
        }

    }
}
