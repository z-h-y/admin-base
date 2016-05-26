<?php namespace App\Http\Middleware;

use Closure;
use App\Utils\Utils;

class DataFormatFilter {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 数据格式过滤
        // 1. 将格式如: test_key 的key转换为: testKey
        // 2. 将mongodb的_id装换为id
        $response = $next($request);
        $jsonData = json_decode($response->getContent(), true);
        if (!$response->headers->get('ignore-data-format', false, true)) {
            if ($jsonData && isset($jsonData['data']) && isset($jsonData['data'][0])) {
                $rawArr = $jsonData['data'];
                $newArr = array();
                foreach ($rawArr as $rawItem) {
                    $newItem = array();
                    foreach ($rawItem as $key => $val) {
                        if (strpos($key, '_') > 0) {
                            $newKey = Utils::snakeToCamel($key);
                            $newItem[$newKey] = $val;
                        } else if ($key === '_id' && isset($rawItem['_id']) && isset($rawItem['_id']['$id'])) {
                            // 处理mongodb的_id
                            $newItem['id'] = $rawItem['_id']['$id'];
                        } else if ($key === '_id' && isset($rawItem['_id'])) {
                            $newItem['id'] = $rawItem['_id'];
                        } else {
                            $newItem[$key] = $val;
                        }
                    }
                    array_push($newArr, $newItem);
                }
                $jsonData['data'] = $newArr;
                $response->setContent(json_encode($jsonData));
            } else if ($jsonData && isset($jsonData['data'])) {
                $rawArr = $jsonData['data'];
                $newArr = array();
                foreach ($rawArr as $key => $val) {
                    if (strpos($key, '_') > 0) {
                        $newKey = Utils::snakeToCamel($key);
                        $newArr[$newKey] = $val;
                    } else if ($key === '_id' && isset($rawArr['_id']) && isset($rawArr['_id']['$id'])) {
                        // 处理mongodb的_id
                        $newArr['id'] = $rawArr['_id']['$id'];
                    } else {
                        $newArr[$key] = $val;
                    }
                }
                $jsonData['data'] = $newArr;
                $response->setContent(json_encode($jsonData));
            }
        } else {
            $response->setContent('{"data":' . $jsonData['data'] . '}'); // unicode fix
        }
        return $response;
    }
}
