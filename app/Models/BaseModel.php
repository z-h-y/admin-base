<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Eloquence\Database\Traits\CamelCaseModel;

/**
 * BaseModel
 */
abstract class BaseModel extends Model {
    use CamelCaseModel;
}
