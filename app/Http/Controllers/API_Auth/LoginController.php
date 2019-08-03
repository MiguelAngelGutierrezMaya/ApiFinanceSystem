<?php

namespace App\Http\Controllers\API_Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\User;
use App\Person;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Screen;
use App\UserScreen;
use App\ScreenAction;
use App\UserScreenAction;

class LoginController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email',
            'user_password' => 'required',
            'user_office' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validacion.', $validator->errors(), 409);
        }

        $data = $request->post();

        $user = User::where('email', '=', $data["user_email"])->get();

        if (count($user) == 0) {
            return $this->sendError('Usuario no encontrado.', ['no result'], 422);
        } else {
            if (!Hash::check($data["user_password"], $user[0]->password)) {
                return $this->sendError('Contraseña incorrecta.', ['no result'], 422);
            } else {
                $user = $user[0];
                $ip = $_SERVER['REMOTE_ADDR'];

                if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                    $ip = $_SERVER["HTTP_CLIENT_IP"];
                } else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                } else if (isset($_SERVER["HTTP_X_FORWARDED"])) {
                    $ip = $_SERVER["HTTP_X_FORWARDED"];
                } else if (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
                    $ip = $_SERVER["HTTP_FORWARDED_FOR"];
                } else if (isset($_SERVER["HTTP_FORWARDED"])) {
                    $ip = $_SERVER["HTTP_FORWARDED"];
                }

                $date = date('Y-m-d H:i:s');
                $user->last_session = $date;
                $user->last_activity = $date;
                // 1 -> cell financiera; 2 -> detalles farello
                $user->office = $data['user_office'];
                $user->last_ip = $ip;
                $user->api_token = md5(uniqid(mt_rand(), false));
                $user->save();

                $oauth_access_tokens = DB::table('oauth_access_tokens')->select('id')->where('user_id', '=', $user->id)->get();

                foreach ($oauth_access_tokens as $oauth_access_token) {
                    DB::table('oauth_refresh_tokens')->where('access_token_id', '=', $oauth_access_token->id)->delete();
                    DB::table('oauth_access_tokens')->where('id', '=', $oauth_access_token->id)->delete();
                }

                $permissions = array();

                $screens = Screen::all();

                foreach ($screens as $screen) {
                    $user_screen = UserScreen::where([
                        ['user_id', '=', $user->id],
                        ['screen_id', '=', $screen->id],
                        ['state', '=', 1]
                    ])
                        ->limit(1)
                        ->get();

                    if (count($user_screen) > 0) {
                        $permissions[$screen->id] = array(
                            'state' => true,
                            'name' => $screen->names,
                            'actions' => array()
                        );
                    } else {
                        $permissions[$screen->id] = array(
                            'state' => false,
                            'name' => $screen->names,
                            'actions' => array()
                        );
                    }
                }

                $screen_actions = ScreenAction::select(
                    'sca.id',
                    'sca.screen_id',
                    'a.visual_name'
                )
                    ->from('screen_actions as sca')
                    ->join('actions as a', 'sca.action_id', '=', 'a.id')
                    ->get();

                foreach ($screen_actions as $screen_action) {
                    $user_screen_action = UserScreenAction::where([
                        ['user_id', '=', $user->id],
                        ['screen_action_id', '=', $screen_action->id],
                        ['state', '=', 1]
                    ])
                        ->limit(1)
                        ->get();

                    if (count($user_screen_action) > 0) {
                        $permissions[$screen_action->screen_id]['actions'][$screen_action->id] = array(
                            'state' => true,
                            'name' => $screen_action->visual_name
                        );
                    } else {
                        $permissions[$screen_action->screen_id]['actions'][$screen_action->id] = array(
                            'state' => false,
                            'name' => $screen_action->visual_name
                        );
                    }
                }

                $person = Person::where('id', '=', $user->id)->get();

                if (count($person) != 0) {
                    $person = $person[0];
                } else {
                    $person = [];
                }
            }
        }

        $url = config('app.url');
        $data_api = config('environments.data_api');

        return $this->sendResponse([
            'user' => $user,
            'person' => $person,
            'permissions' => $permissions,
            'url' => $url . '/oauth/token',
            'params' => [
                'username' => $user->email,
                'password' => $data["user_password"],
                'grant_type' => 'password',
                'client_id' => $data_api["client_id"],
                'client_secret' => $data_api["client_secret"]
            ]
        ], 'Autenticación exitosa');
    }
}
