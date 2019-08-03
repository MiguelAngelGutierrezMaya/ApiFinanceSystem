<?php

namespace App\Http\Controllers\API_Auth;

use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\User;
use App\Password_reset;
use Illuminate\Http\Request;
use DateTime;
use App\Person;
use App\Notifications\NotifyChangePassword;

class ResetPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

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
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string|null $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_passreset_code' => 'required',
            'password_reset_token_password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validacion.', $validator->errors(), 409);
        }

        $data = $request->all();

        $user = User::where('passreset_code', $data["user_passreset_code"])->limit(1)->get();

        if (count($user) == 0) {
            return $this->sendError('Error, token de usuario incorrecto', ['no result'], 422);
        }

        $password_reset = Password_reset::where('token_password', $data["password_reset_token_password"])->limit(1)->get();

        if (count($password_reset) == 0) {
            return $this->sendError('Error, token de password incorrecto', ['no result'], 422);
        }

        $user = $user[0];
        $password_reset = $password_reset[0];

        if ($user->id != $password_reset->user_id) {
            return $this->sendError('Error, el token de seguridad no coincide con el usuario que lo solicitó', ['no result'], 422);
        }

        if ($password_reset->state != 1) {
            return $this->sendError('Error, la solicitud de restablecimiento ya se utilizó o se venció', ['no result'], 422);
        }

        $date_password_reset = new Datetime($password_reset->created_at);
        $current_date = new DateTime(date("Y-m-d H:i:s"));
        $interval = $date_password_reset->diff($current_date);
        $interval_time = $interval->format('%h');
        $interval_day = $interval->format('%d');

        if ($interval_time >= 1 || $interval_day > 0) {
            Password_reset::where('id', '=', $password_reset->id)->update(array('state' => 0));
            return $this->sendError('Error, Limite de tiempo excedido, se debe generar una nueva solicitud', ['no result'], 422);
        }

        Password_reset::where('id', '=', $password_reset->id)->update(array('updated_at' => date("Y-m-d H:i:s")));

        return $this->sendResponse([
            'user_passreset_code' => $data["user_passreset_code"],
            'user_id' => $user->id,
            'password_reset_token_password' => $data["password_reset_token_password"]
        ], 'Result ok');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return $this->sendError('Error de validacion.', $validator->errors(), 409);
        }

        $data = $request->all();

        $user = User::where([
            ['id', $data['user_id']],
            ['passreset_code', $data['user_passreset_code']]
        ])->get();

        if (count($user) == 0) {
            return $this->sendError('Error, token de usuario incorrecto', ['no result'], 422);
        }

        $user = $user[0];

        $password_reset = Password_reset::where([
            ['token_password', '=', $data['password_reset_token_password']],
            ['user_id', '=', $user->id]
        ])->get();

        if (count($password_reset) == 0) {
            return $this->sendError('Error, token de password incorrecto', ['no result'], 422);
        } else if ($password_reset[0]->state == 0) {
            return $this->sendError('Error, el token ya se usó anteriormente', ['no result'], 422);
        }

        $person = Person::where('id', $user->id)->get();

        if (count($person) == 0) {
            return $this->sendError('No existe una persona registrada con ese usuario, favor comunicarse con soporte', ['no result'], 422);
        }

        $person = $person[0];
        $password_reset = $password_reset[0];
        $password_reset->state = 0;

        if ($password_reset->save()) {
            $user->password = bcrypt($data['user_password']);
            $user->passreset_code = null;

            if ($user->save()) {
                $person->notify(new NotifyChangePassword($person));
                return $this->sendResponse(['msj' => 'Contraseña modificada exitosamente'], 'Result ok');
            } else {
                return $this->sendError('Un error al hacer el cambio de contraseña, por favor comunicarse con soporte', ['no result'], 422);
            }
        } else {
            return $this->sendError('Un error al hacer el registro del cambio de contraseña, por favor comunicarse con soporte', ['no result'], 422);
        }
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'user_passreset_code' => 'required',
            'user_id' => 'required',
            'password_reset_token_password' => 'required',
            'user_password' => 'required|confirmed|min:6'
        ];
    }
}
