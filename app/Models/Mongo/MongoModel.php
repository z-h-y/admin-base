<?php namespace App\Models\Mongo;

use Jenssegers\Mongodb\Model as Eloquent;
use App\Utils\Constants;
use Eloquence\Database\Traits\CamelCaseModel;

abstract class MongoModel extends Eloquent {
    use CamelCaseModel;

    protected $connection = Constants::CONN_MONGO;

}
