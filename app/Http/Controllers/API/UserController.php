<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use DateTime;
use App\User;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'user_api_token' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validacion.', $validator->errors(), 409);
        }

        $data = $request->post();

        $user = User::where([
            ['id', '=', $data["user_id"]],
            ['api_token', '=', $data["user_api_token"]]
        ])->get();

        if (count($user) == 0) {
            return $this->sendError('Usuario no encontrado. revisa el id o token de usuario', ['no result'], 422);
        } else {
            $user = $user[0];
            $user->api_token = null;
            $user->save();

            $oauth_access_tokens = DB::table('oauth_access_tokens')->select('id')->where('user_id', '=', $user->id)->get();

            foreach ($oauth_access_tokens as $oauth_access_token) {
                DB::table('oauth_refresh_tokens')->where('access_token_id', '=', $oauth_access_token->id)->delete();
                DB::table('oauth_access_tokens')->where('id', '=', $oauth_access_token->id)->delete();
            }
        }

        return $this->sendResponse(['success'], 'La sesion se ha cerrado correctamente');
    }

    /**
     * Get Collectors function
     */
    public function getCollectors(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validacion.', $validator->errors(), 409);
        }

        $user_id = $request->get("user_id");
        $verify = $this->verifySession($user_id);

        if (!$verify["bool"]) {
            return $this->sendError($verify["msj"], ['no result'], 422);
        }

        $users = User::select(
            'users.id',
            'persons.names',
            'persons.surnames'
        )
            ->join('persons', 'users.id', '=', 'persons.id')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where([
                ['roles.id', '=', 2],
                ['users.state', '=', 1]
            ])
            ->orderBy('users.id', 'desc')
            ->get();

        return [
            'users' => $users
        ];
    }

    /**
     * Verify Session Lifetime function
     */
    private function verifySession($user_id)
    {
        $user = User::where([
            ['id', '=', $user_id]
        ])->get();

        if (count($user) == 0) {
            return ['bool' => false, 'msj' => 'Usuario no encontrado. revisa el id o token de usuario'];
        } else {

            $user = $user[0];
            $lifetime = config("session.lifetime");
            $last_activity = new DateTime($user->last_activity);
            $current_datetime = new DateTime(date("Y-m-d H:i:s"));
            $diff = $last_activity->diff($current_datetime);

            $bool = true;
            $msj = '';

            if ($diff->i > $lifetime) {
                $user->api_token = null;
                $oauth_access_tokens = DB::table('oauth_access_tokens')->select('id')->where('user_id', '=', $user->id)->get();

                foreach ($oauth_access_tokens as $oauth_access_token) {
                    DB::table('oauth_refresh_tokens')->where('access_token_id', '=', $oauth_access_token->id)->delete();
                    DB::table('oauth_access_tokens')->where('id', '=', $oauth_access_token->id)->delete();
                }

                $bool = false;
                $msj = 'Limite de tiempo excedido';
            } else {
                $user->last_activity = date("Y-m-d H:i:s");
            }

            $user->save();
            return ['bool' => $bool, 'msj' => $msj];
        }
    }
}
