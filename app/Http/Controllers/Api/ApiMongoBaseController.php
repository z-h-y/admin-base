<?php namespace App\Http\Controllers\Api;

use App\Utils\Constants;

abstract class ApiMongoBaseController extends ApiBaseController {
    
    protected $connection = Constants::CONN_MONGO;
    
}
