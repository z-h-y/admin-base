<?php

return array(
    'app_url' => env('LEANCOULD_APP_URL'),
    'app_id' => env('LEANCOULD_APP_ID'),
    'app_key' => env('LEANCOULD_APP_KEY', env('LEANCOULD_APP_Key')),
    'master_key' => env('LEANCOULD_MASTER_KEY', env('LEANCOULD_MASTER_Key')),
    'backup_default_list' => env('LEANCOULD_BACKUP_DEFAULT_LIST', '_Conversation,_File,_Followee,_Follower,_Installation,_Notification,_Role,_Status,_User'),
    'backup_list' => env('LEANCOULD_BACKUP_LIST'),
    'backup_interval' => env('LEANCOULD_BACKUP_INTERVAL'),
    'backup_default_interval' => env('LEANCOULD_BACKUP_DEFAULT_INTERVAL', 4),
);
