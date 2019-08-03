<?php

namespace App\Http\Controllers\API_Auth;

use App\Notifications\ResetPasswordNotification;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\User;
use App\Person;
use App\Password_reset;
use Illuminate\Http\Request;

class ForgotPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
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
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validacion.', $validator->errors(), 409);
        }

        $data = $request->all();
        $user = User::where('email', $data['user_email'])->get();

        if (count($user) != 0) {
            $user = $user[0];

            if ($user->state == 0) {
                return $this->sendError('El Usuario no se encuentra activo.', ['no result'], 422);
            }
        } else {
            return $this->sendError('Usuario no encontrado con el email ingresado.', ['no result'], 422);
        }

        $person = Person::where('id', $user->id)->get();

        if (count($person) != 0) {
            $person = $person[0];

            $user->passreset_code = md5(uniqid(mt_rand(), false));
            $user->last_req_pass = date('Y-m-d H:i:s');
            $user->save();
        } else {
            return $this->sendError('No existe una persona registrada con el email ingresado. Por favor comunicate con soporte', ['no result'], 422);
        }

        $token_password = md5(uniqid(mt_rand(), false));

        Password_reset::create([
            'user_id' => $user->id,
            'token_password' => $token_password,
            'state' => 1,
        ]);

        $user->notify(new ResetPasswordNotification($person, $user->passreset_code, $token_password));

        return $this->sendResponse([
            'msj' => 'Se ha enviado la notificación al correo ingresado'
        ], 'Email enviado con exito');
    }
}
