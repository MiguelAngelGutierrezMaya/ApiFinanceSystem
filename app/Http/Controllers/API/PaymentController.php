<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use App\User;
use Validator;
use DateTime;
use App\Financing;
use App\Payment;
use App\Location;
use App\FinancialVisit;
use App\Note;
use App\NoteFinancialVisit;
use App\NotePayment;

class PaymentController extends BaseController
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

        $payments = Payment::where('financing_id', '=', $data["financing_id"])->orderBy('id', 'desc')->get();
        $paymentsAmount = Payment::select('id', 'amount', 'paid_date')->where([
            ['financing_id', '=', $data["financing_id"]],
            ['state', '!=', 0]
        ])->orderBy('id', 'desc')->get();

        if (count($payments) == 0) {
            $payments = [];
        }
        if (count($paymentsAmount) == 0) {
            $paymentsAmount = [];
        }

        return $this->sendResponse([
            'payments' => $payments,
            'paymentsAmount' => $paymentsAmount
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
            'financing_balance' => 'required',
            'payment_date_payment' => 'required',
            'payment_amount' => 'required',
            'note_description' => 'required',
            'user_id' => 'required',
            'location_latitude' => 'required',
            'location_longitude' => 'required'
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

        if ($data["payment_amount"] < 0) {
            $errors[] = 'El valor del abono no puede ser menor que 0';
            $bool = true;
        }

        if ($data["financing_balance"] < $data["payment_amount"]) {
            $errors[] = 'El saldo del financiamiento no puede ser menor al valor de pago en el abono';
            $bool = true;
        }

        $financing = Financing::select(
            'f.date_financing',
            'f.total_value',
            'fd.date_init',
            'fd.daily_quota',
            'fr.date_financing_requests',
            'fr.client_id',
            'pe.names',
            'l.area_id'
        )
            ->from('financings as f')
            ->join('financing_details as fd', 'f.id', '=', 'fd.id')
            ->join('financing_requests as fr', 'f.financing_request_id', '=', 'fr.id')
            ->join('persons as pe', 'fr.client_id', '=', 'pe.id')
            ->join('locations as l', 'pe.location_id', '=', 'l.id')
            ->where('f.id', '=', $data["financing_id"])
            ->limit(1)
            ->get();

        if (count($financing) == 0) {
            $errors[] = 'Error, no existe el financiamiento, por favor comunicate con soporte';
            $bool = true;
        }

        $lat = $data["location_latitude"];
        $lng = $data["location_longitude"];

        $ch = curl_init("https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&key=" . config('google.places.key'));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        $result = curl_exec($ch);

        curl_close($ch);

        if ($result) {
            $json = json_decode($result, true);
            $address = $json["results"][0]["formatted_address"];
        } else {
            $errors[] = "Error en la petición de la direccion";
            $bool = true;
        }

        $lastDate = $financing[0]->date_init;

        $payments = Payment::where([['financing_id', '=', $data["financing_id"]], ['state', '!=', 0]])->orderBy('id', 'desc')->get();

        $value = $data["payment_amount"];

        if (count($payments) != 0) {
            foreach ($payments as $row) {
                $value += $row->amount;
            }

            if ($value > $financing[0]->total_value) {
                $errors[] = "Los abonos registrados hasta la fecha ($" . number_format($value, 2, '.', '') . ") superan el valor total del financiamiento: $" . number_format($financing[0]->total_value, 2, '.', '');
                $bool = true;
            }
        }

        if ($bool) {
            return $this->sendError('Error de validacion.', json_encode($errors) . ' estado del result: ' . $result, 409);
        }

        $paid_days = floor($value / $financing[0]->daily_quota);
        $lastDate = strtotime('+' . $paid_days . 'day', strtotime($lastDate));
        $lastDate = date('Y-m-d', $lastDate);

        DB::beginTransaction();

        try {

            $location = new Location();
            $location->area_id = $financing[0]->area_id;
            $location->latitude = $lat;
            $location->longitude = $lng;
            $location->address = $address;
            $location->save();

            $financial_visit = new FinancialVisit();
            $financial_visit->financing_id = $data["financing_id"];
            $financial_visit->user_id = $data["user_id"];
            $financial_visit->location_id = $location->id;
            $financial_visit->date_financial_visit = $data["payment_date_payment"];
            $financial_visit->save();

            $note = new Note();
            $note->date_note = date('Y-m-d');
            $note->description = $data["note_description"];
            $note->save();

            $note_financial_visit = new NoteFinancialVisit();
            $note_financial_visit->financial_visit_id = $financial_visit->id;
            $note_financial_visit->note_id = $note->id;
            $note_financial_visit->save();

            $payment = new Payment();
            $payment->person_id = $financing[0]->client_id;
            $payment->user_id = $data["user_id"];
            $payment->financing_id = $data["financing_id"];
            $payment->financial_visit_id = $financial_visit->id;
            $payment->date_payment = $data["payment_date_payment"];
            $payment->paid_date = $lastDate;
            $payment->amount = $data["payment_amount"];
            $payment->save();

            $note = new Note();
            $note->date_note = date('Y-m-d');
            $note->description = 'Se registra pago por el modulo de financiamientos Detalles: monto->'
                . $data["payment_amount"] . ' Registrado por->'
                . $user->email
                . ' Id_abono->'
                . $payment->id;
            $note->save();

            $note_payment = new NotePayment();
            $note_payment->payment_id = $payment->id;
            $note_payment->note_id = $note->id;
            $note_payment->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError('Error de BD.', ['Hubo un error al registrar en la base de datos, por favor intente nuevamente o comunicate con soporte ' . $e->getMessage()], 409);
        }

        return $this->sendResponse(['success'], 'El abono ha sido registrado');
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
