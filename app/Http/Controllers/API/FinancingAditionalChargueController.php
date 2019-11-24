<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use App\Financing;
use App\FinancingAditionalChargue;
use App\FinancingDetail;
use App\Frecuency;
use App\User;
use App\UserWallet;
use App\Wallet;
use Validator;
use DateTime;
use App\FinancingRequest;

class FinancingAditionalChargueController extends BaseController
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'financing_id' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validacion.', $validator->errors(), 409);
        }

        $data = $request->all();

        $user = User::where('id', '=', $data["user_id"])->get();

        if (count($user) == 0) {
            return $this->sendError('Usuario no encontrado.', ['no result'], 422);
        } else {
            $user = $user[0];
        }

        $user_id = $data["user_id"];
        $verify = $this->verifySession($user_id);

        if (!$verify["bool"]) {
            return $this->sendError($verify["msj"], ['no result'], 422);
        }

        $financingAditionalChargues = FinancingAditionalChargue::where('financing_id', '=', $data['financing_id'])->orderBy('id', 'desc')->get();

        if(count($financingAditionalChargues) == 0) {
            $financingAditionalChargues = [];
        }

        return $this->sendResponse([
            'financingAditionalChargues' => $financingAditionalChargues
        ], 'Result OK');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'financing_id' => 'required',
            'user_id' => 'required',
            'financing_aditional_chargue_date_aditional_chargue' => 'required',
            'financing_aditional_chargue_amount' => 'required',
            'financing_aditional_chargue_type' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validacion.', $validator->errors(), 409);
        }

        $data = $request->post();

        $user = User::where('id', '=', $data["user_id"])->get();

        if (count($user) == 0) {
            return $this->sendError('Usuario no encontrado.', ['no result'], 422);
        } else {
            $user = $user[0];
        }

        $user_id = $data["user_id"];
        $verify = $this->verifySession($user_id);

        if (!$verify["bool"]) {
            return $this->sendError($verify["msj"], ['no result'], 422);
        }

        $errors = [];
        $bool = false;

        if ($data["financing_aditional_chargue_amount"] <= 0) {
            $errors[] = "El valor del monto no puede ser menor o igual a 0";
            $bool = true;
        }
        if ($data["financing_aditional_chargue_type"] === 0) {
            $errors[] = "Debe seleccionar el tipo del cargo adicional";
            $bool = true;
        }
        if($data["financing_aditional_chargue_type"] != "discount" && $data["financing_aditional_chargue_type"] != "fine") {
            $errors[] = "Solo se permiten dos tipos de cargo adicional, multa(fine) o descuento (discount)";
            $bool = true;
        }

        $financing = Financing::findOrFail($data["financing_id"]);

        if (($data["financing_aditional_chargue_type"] == 'discount') && (($financing->balance - $data["financing_aditional_chargue_amount"]) < 0)) {
            $errors[] = "Error el valor del descuento supera el saldo del financiamiento";
            $bool = true;
        }

        $user_wallet = UserWallet::where([
            ['user_id', '=', $financing->collector_id],
            ['branch_office_id', '=', $financing->branch_office_id]
        ])->get();

        if (count($user_wallet) == 0) {
            $errors[] = "Error, no existe una cartera asignada para el conductor de este financiamiento";
            $bool = true;
        } else {
            $user_wallet = $user_wallet[0];
        }

        if($financing->state == 'paid') {
            $errors[] = "Error, el financiamiento ya está finalizado";
            $bool = true;
        }

        if (!$bool) {
            DB::beginTransaction();

            try {
                $financingAditional_chargue = new FinancingAditionalChargue();
                $financingAditional_chargue->financing_id = $data["financing_id"];
                $financingAditional_chargue->user_id = $data["user_id"];
                $financingAditional_chargue->date_aditional_chargue = $data["financing_aditional_chargue_date_aditional_chargue"];
                $financingAditional_chargue->amount = $data["financing_aditional_chargue_amount"];
                $financingAditional_chargue->type = $data["financing_aditional_chargue_type"];
                $financingAditional_chargue->save();

                $financing_detail = FinancingDetail::findOrFail($financing->id);
                $frecuency = Frecuency::findOrFail($financing_detail->frecuency_id);
                $wallet = Wallet::findOrFail($user_wallet->wallet_id);

                if ($data["financing_aditional_chargue_type"] == 'fine') {
                    $financing->total_value += $data["financing_aditional_chargue_amount"];
                    $financing->balance += $data["financing_aditional_chargue_amount"];
                    $financing_detail->fines += $data["financing_aditional_chargue_amount"];
                    $wallet->amount += $data["financing_aditional_chargue_amount"];
                } else if ($data["financing_aditional_chargue_type"] == 'discount') {
                    $financing->total_value -= $data["financing_aditional_chargue_amount"];
                    $financing->balance -= $data["financing_aditional_chargue_amount"];
                    $wallet->amount -= $data["financing_aditional_chargue_amount"];
                    $financing_detail->discounts += $data["financing_aditional_chargue_amount"];

                    /** Requerimiento solicitado (cambiar estado si el descuento es por el total del financiamiento) */
                    if ($financing->balance <= 0) {
                        $financing->state = 'paid';
                    }
                }

                $financing_detail->daily_quota = $financing->total_value / $financing_detail->quotas;
                $financing_detail->frecuency_quota = $financing_detail->daily_quota * $frecuency->days;

                $wallet->save();
                $financing_detail->save();
                $financing->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                $errors[] = 'Hubo un error al registrar en la base de datos, por favor intente nuevamente o comunicate con soporte' . $e->getMessage();
                $bool = true;
            }
        }

        if ($bool) {
            return $this->sendError('Error de validacion.', json_encode($errors), 409);
        }

        return $this->sendResponse(['success'], 'El cargo adicional ha sido registrado');
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
