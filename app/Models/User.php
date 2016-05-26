<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Hash;

/**
 * User
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $rememberToken
 * @property string $avatar
 * @property boolean $active
 * @property boolean $sso
 */
class User extends BaseModel implements AuthenticatableContract {

	use Authenticatable, EntrustUserTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token', 'pivot'];

	/**
	 * Prepare user model before save.
	 */
	public function prepare() {
		if ($this->password) {
			$this->password = Hash::make($this->password);
		}
	}
}
