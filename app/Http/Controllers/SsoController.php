<?php namespace App\Http\Controllers;

use Redirect;
use Config;
use Illuminate\Http\Request;
use Auth;
use Session;
use App\Utils\Utils;
use App\Models\User;
use Illuminate\Routing\Controller;

class SsoController extends Controller {
        
    /**
     * Login sso user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login() {
        // redirect to passport sso page
        $ssoUrl = Config::get('sso.site_url');
        $siteId = Config::get('sso.site_id');
        $siteSecret = Config::get('sso.site_secret');
        if ($ssoUrl && $siteId && $siteSecret) {
            $siteUrl = url('admin');
            $ssoUrl = $ssoUrl . '?' . http_build_query(
                array(
                    'sso_action' => 'login',
                    'return_url' => $siteUrl,
                ));
            return Redirect::to($ssoUrl);
        } else {
            return 'Invalid config for sso site!';
        }
    }


    /**
     * Callback for sso
     *
     * @param Request $request
     * @return mixed
     */
    public function callback(Request $request) {
        // process sso user login
        $ssoAction = $request->input('sso_action');
        $ssoToken = $request->input('sso_token');
        $username = $request->input('username');
        $email = $request->input('email');
        $avatar = $request->input('avatar');
        $callback = $request->input('callback');

        // 登录或者退出SSO用户，如果用户不存在则先创建
        if ($callback && $ssoAction && ($username != 'admin') && $this->checkToken($ssoToken, $username)) {
            $user = User::where('email', '=', $email)->first();
            $user = isset($user) ? $user : User::where('name', '=', $username)->first();

            if (!$user) {
                $user = User::create(array(
                    'name' => $username,
                    'email' => $email,
                    'active' => true,
                    'avatar' => $avatar,
                    'password' => Utils::generatePassword($email, $username),
                    'sso' => true,
                ));
            }
            
            if ($user) {
                switch ($ssoAction) {
                    case 'login':
                        $useId = $user->id;
                        Auth::loginUsingId($useId);
                        Session::put('sso', '1');
                        break;

                    case 'logout':
                        Auth::logout();
                        Session::forget('sso');
                        break;

                    default:
                        break;
                }
            }
            return response()->json(array('msg' => 'Sso success!'))
                ->setCallback($callback);
        } else {
            return response()->json(array('error' => true, 'msg' => 'Sso failed!'))
                ->setCallback($callback);
        }
    }

    protected function checkToken($token, $username) {
        $siteId = Config::get('sso.site_id');
        $siteSecret = Config::get('sso.site_secret');

        $result = false;
        if ($siteId && $siteSecret) {
            $myToken = md5(md5($siteId . $siteSecret) . $username);
            if ($token == $myToken) {
                $result = true;
            }
        }
        return $result;
    }
}
