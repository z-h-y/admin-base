<?php namespace App\Models;

/**
 * Resource
 *
 * @property integer $id
 * @property string $group
 * @property integer $value
 * @property string $name
 * @property string $comment
 * @property boolean $active
 */
class Codec extends BaseModel {
    protected $table = 'codecs';
    
}
