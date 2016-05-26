<?php namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Hash;
use App\Models\User;
use App\Http\Controllers\Api\ApiBaseController;
use App\Utils\Utils;
use Auth;

class UserController extends ApiBaseController {

    protected $tableName = 'users';

    protected $hiddenKeys = array('remember_token');

    protected $user;

    protected $rules = array(
        'name' => 'required|min:3',
        'password' => 'required|min:8',
        'password_confirmation' => 'required|min:8',
        'email' => 'required|email',
    );

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $includeRoles = $request->input('includeRoles');
        if ($includeRoles) {
            return $this->buildResponse($this->applyFilter($this->getQuery()), false, function($data) {
                $result = $data;
                if ($data && is_array($data)) {
                    $result = array_map(function($item) {
                        $user = User::find($item['id']);
                        if ($user) {
                            $item['roles'] = $user->roles->toArray();
                        }
                        return $item;
                    }, $data);
                }
                return $result;
            });
        } else {
            return $this->buildResponse($this->applyFilter($this->getQuery()));
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validate = $this->validate();
        if ($validate) {
            return $this->errorResponse($validate);
        }

        $user = $this->user;
        $name = $request->input('name');
        $email = $request->input('email');
        $pwd = $request->input('password');
        $pwdConfirm = $request->input('password_confirmation');
        $active = ($request->input('active') == 1) ? 1 : 0;

        if (!Utils::checkComplexPassword($pwd)) {
            return $this->errorResponse('请使用数字跟字母组合的复杂密码！');
        }

        $user->name = isset($name) ? $name : $user->name;
        $user->email = isset($email) ? $email : $user->email;
        $user->active = isset($active) ? $active : $user->active;

        $error = '';
        if ($this->updateFlag) { // update user
            $existUser = User::where('email', '=', $user->email)->where('id', '!=', $this->user->id)->first();
            $existUser = isset($existUser) ? $existUser : User::where('name', '=', $user->name)->where('id', '!=', $this->user->id)->first();
            if ($existUser) {
                return $this->errorResponse('使用相同用户名或邮箱的用户已经存在!');
            } else if ($pwd && $pwdConfirm) {
                if ($pwd == $pwdConfirm) {
                    if (($user->password != $pwd) && (!Hash::check($pwd, $user->password))) {
                        // must update password
                        $user->password = $pwd;
                        $user->prepare();
                    }
                }
            }
        } else { // create user
            $existUser = User::where('email', '=', $user->email)->first();
            $existUser1 = User::where('name', '=', $user->name)->first();
            $existUser = isset($existUser) ? $existUser : $existUser1;
            if ($existUser) {
                return $this->errorResponse('使用相同用户名或邮箱的用户已经存在!');
            } else if ($pwd && $pwdConfirm) {
                if ($pwd == $pwdConfirm) {
                    if (!Hash::check($pwd, $user->password)) {
                        $user->password = $pwd;
                        $user->prepare();
                    }
                } else {
                    $error = '密码与密码确认不正确!';
                }
            } else {
                $error = '密码与密码确认为必填项!';
            }
        }

        if (!$error) {
            $user->save();
            if ($user->id) {
                return $this->show($user->id);
            }
        } else {
            return $this->errorResponse($error);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if ($id) {
            $this->user = User::find($id);
            if ($this->user) {
                $this->updateFlag = true;
                return $this->store($request);
            }
        }
    }

    /**
     * Get user's roles
     *
     * @param $id
     * @return Response
     */
    public function getRoles($id)
    {
        $user = User::find($id);
        return $this->jsonResponse($user->roles->toArray(), array('pivot'));
    }

    /**
     * Update user's roles
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function updateRoles(Request $request, $id) {
        $user = User::find($id);
        $ids = $request->input('ids');
        if ($ids) {
            $roleIds = array_map(function($item) {
                return intval(trim($item));
            }, explode(',', $ids));
        } else {
            $roleIds = array();
        }

        if ($user) {
            $user->roles()->sync($roleIds);
            return $this->jsonResponse($user);
        }
    }

    /**
     * Get user's permissions
     *
     * @param $id
     * @return Response
     */
    public function getPermissions($id)
    {
        $user = User::find($id);
        $result = array();
        foreach ($user->roles as $role) {
            foreach ($role->perms as $permission) {
                array_push($result, $permission);
            }
        }
        return $this->jsonResponse($result, array('pivot'));
    }

    /**
     * 更新当前登录用户的个人信息，目前只允许修改密码
     *
     * @param Request $request
     * @return Response
     */
    public function updateUserProfile(Request $request) {
        $oldPassword = $request->input('oldPassword');
        $newPassword = $request->input('newPassword');
        $newPasswordRepeat = $request->input('newPasswordRepeat');

        if ($oldPassword && $newPassword && $newPasswordRepeat) {
            $user = Auth::user();
            if (!$user) {
                return $this->errorResponse('请先登录.');
            }

            if (Auth::validate(['password' => $oldPassword, 'name' => $user->name])) {
                if ($newPassword && $newPasswordRepeat) {
                    if ($newPassword == $newPasswordRepeat) {
                        $user->password = $newPassword;
                        $user->prepare();
                        $user->save();
                        return $this->successResponse();
                    } else {
                        return $this->errorResponse('新密码重复填写不一致');
                    }
                } else {
                    return $this->errorResponse('新密码或新密码重复不存在！');
                }
            } else {
                return $this->errorResponse('旧密码错误!');
            }
        } else {
            return $this->errorResponse('Some of the following parameters are missing: oldPassword, newPassword, newPasswordRepeat.');
        }
    }
}
