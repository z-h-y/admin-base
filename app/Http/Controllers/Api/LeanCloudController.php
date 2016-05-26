<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Api\ApiBaseController;
use Config;

class LeanCloudController extends ApiBaseController
{
    public function search(Request $request)
    {
        $time = $request->input('time');
        if (!$time) {
            return $this->errorResponse('Missing or invalid parameter: time');
        }

        $appUrl = Config::get('leancloud.app_url');
        $appId = Config::get('leancloud.app_id');
        $masterKey = Config::get('leancloud.master_key');
        if ($appUrl && $appId && $masterKey) {
            $result = array();
            $result['leanCloudAppUrl'] = $appUrl;
            $result['leanCloudAppId'] = $appId;
            $reqSign = md5($time . $masterKey) . ',' . $time . ',master';
            $result['leanCloudReqSign'] = $reqSign;
            return $this->jsonResponse($result);
        } else {
            return $this->errorResponse('Invalid config for LeanCloud!');
        }

    }
}
