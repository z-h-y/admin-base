<?php namespace App\Http\Controllers\Api;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SessionController extends ApiBaseController {

    protected $rules = array(
        'name' => 'required|min:3',
        'password' => 'required|min:6',
    );

    /**
     * Check user login status
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if (Auth::check()) {
            return $this->jsonResponse(Auth::user());
        } else {
            return $this->jsonResponse(array());
        }
    }

    /**
     * Create session (user login)
     *
     * @param  Request  $request 
     * @return Response
     */
    public function store(Request $request)
    {
        $validate = $this->validate();
        if ($validate) {
            return $this->errorResponse($validate);
        }

        $error = null;
        $name = $request->input('name');
        $password = $request->input('password');
        $remember = $request->input('remember', false);

        if ($name && $password) {
            $login = Auth::attempt(array('name' => $name, 'password' => $password, 'active' => 1), $remember);
            if (!$login) {
                $login = Auth::attempt(array('email' => $name, 'password' => $password, 'active' => 1), $remember);
            }

            if (!$login) {
                $error = '用户名或密码不正确';
            }
        } else {
            $error = '用户名和密码是必要的';
        }

        if ($error) {
            return $this->errorResponse($error);
        } else {
            return $this->jsonResponse(Auth::user());
        }
    }

    /**
     * Logout
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Auth::logout();
        return $this->successResponse();
    }
}
