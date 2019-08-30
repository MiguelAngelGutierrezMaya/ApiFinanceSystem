<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use App\Financing;
use App\Payment;
use App\User;
use Validator;
use DateTime;
use App\FinancingRequest;

class FinancingController extends BaseController
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
            'user_id' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validacion.', $validator->errors(), 409);
        }

        $user_id = $request->get("user_id");
        $verify = $this->verifySession($user_id);

        if (!$verify["bool"]) {
            return $this->sendError($verify["msj"], ['no result'], 422);
        }

        $find = $request->find;
        $column = $request->column;
        $type = $request->type;

        if ($type != 1 && $type != 2) {
            return $this->sendError("Error, la variable type debe ser 1 o 2", ['error type'], 422);
        }

        $data = array(
            'f.id',
            'f.date_financing',
            'f.total_value',
            'f.balance',
            'f.state',
            'fr.date_financing_requests',
            'fr.second_deptor_id',
            'p.name as product_name',
            'pe1.names as names_p1',
            'pe1.surnames as surnames_p1',
            'pe2.names as names_p2',
            'pe2.surnames as surnames_p2',
            'pe3.names as names_p3',
            'pe3.surnames as surnames_p3'
        );

        $totalPendingFinancings = [];

        if ($find == '') {
            if ($type == 1) {
                $where = [
                    ['f.state', '=', 'pending'],
                    ['f.collector_id', '=', $user_id]
                ];
                $financings = Financing::select($data)->from('financings as f')
                    ->join('financing_requests as fr', 'f.financing_request_id', '=', 'fr.id')
                    ->join('products as p', 'fr.product_id', '=', 'p.id')
                    ->join('persons as pe1', 'fr.client_id', '=', 'pe1.id')
                    ->join('persons as pe2', 'fr.deptor_id', '=', 'pe2.id')
                    ->join('persons as pe3', 'f.collector_id', '=', 'pe3.id')
                    ->where($where)
                    ->orderBy('f.id', 'desc')
                    ->paginate(5);

                $totalPendingFinancings = Financing::select($data)->from('financings as f')
                    ->join('financing_requests as fr', 'f.financing_request_id', '=', 'fr.id')
                    ->join('products as p', 'fr.product_id', '=', 'p.id')
                    ->join('persons as pe1', 'fr.client_id', '=', 'pe1.id')
                    ->join('persons as pe2', 'fr.deptor_id', '=', 'pe2.id')
                    ->join('persons as pe3', 'f.collector_id', '=', 'pe3.id')
                    ->where($where)
                    ->orderBy('f.id', 'desc')
                    ->get();
            } else if ($type == 2) {
                $financings = Financing::select($data)->from('financings as f')
                    ->join('financing_requests as fr', 'f.financing_request_id', '=', 'fr.id')
                    ->join('products as p', 'fr.product_id', '=', 'p.id')
                    ->join('persons as pe1', 'fr.client_id', '=', 'pe1.id')
                    ->join('persons as pe2', 'fr.deptor_id', '=', 'pe2.id')
                    ->join('persons as pe3', 'f.collector_id', '=', 'pe3.id')
                    ->where([
                        ['f.state', '!=', 'pending'],
                        ['f.collector_id', '=', $user_id]
                    ])
                    ->orderBy('f.id', 'desc')
                    ->paginate(5);
            }
        } else {

            $arrayColumn = explode('-', $column);

            if (trim($arrayColumn[0]) == 1) {
                $table = 'pe1.';
            }

            if (trim($arrayColumn[0]) == 2) {
                $table = 'pe2.';
            }

            if (trim($arrayColumn[0]) == 3) {
                $table = 'pe3.';
            }

            if ($type == 1) {
                $where = [
                    ['f.state', '=', 'pending'],
                    [$table . $arrayColumn[1], 'like', '%' . $find . '%'],
                    ['f.collector_id', '=', $user_id]
                ];

                $financings = Financing::select($data)->from('financings as f')
                    ->join('financing_requests as fr', 'f.financing_request_id', '=', 'fr.id')
                    ->join('products as p', 'fr.product_id', '=', 'p.id')
                    ->join('persons as pe1', 'fr.client_id', '=', 'pe1.id')
                    ->join('persons as pe2', 'fr.deptor_id', '=', 'pe2.id')
                    ->join('persons as pe3', 'f.collector_id', '=', 'pe3.id')
                    ->where($where)
                    ->orderBy('f.id', 'desc')
                    ->paginate(5);

                $totalPendingFinancings = Financing::select($data)->from('financings as f')
                    ->join('financing_requests as fr', 'f.financing_request_id', '=', 'fr.id')
                    ->join('products as p', 'fr.product_id', '=', 'p.id')
                    ->join('persons as pe1', 'fr.client_id', '=', 'pe1.id')
                    ->join('persons as pe2', 'fr.deptor_id', '=', 'pe2.id')
                    ->join('persons as pe3', 'f.collector_id', '=', 'pe3.id')
                    ->where($where)
                    ->orderBy('f.id', 'desc')
                    ->get();
            } else if ($type == 2) {
                $financings = Financing::select($data)->from('financings as f')
                    ->join('financing_requests as fr', 'f.financing_request_id', '=', 'fr.id')
                    ->join('products as p', 'fr.product_id', '=', 'p.id')
                    ->join('persons as pe1', 'fr.client_id', '=', 'pe1.id')
                    ->join('persons as pe2', 'fr.deptor_id', '=', 'pe2.id')
                    ->join('persons as pe3', 'f.collector_id', '=', 'pe3.id')
                    ->where([
                        ['f.state', '!=', 'pending'],
                        [$table . $arrayColumn[1], 'like', '%' . $find . '%'],
                        ['f.collector_id', '=', $user_id]
                    ])
                    ->orderBy('f.id', 'desc')
                    ->paginate(5);
            }
        }

        return $this->sendResponse([
            'pagination' => [
                'count' => $financings->count(),
                'onFirstPage' => $financings->onFirstPage(),
                'hasMorePages' => $financings->hasMorePages(),
                'total' => $financings->total(),
                'current_page' => $financings->currentPage(),
                'per_page' => $financings->perPage(),
                'last_page' => $financings->lastPage(),
                'from' => $financings->firstItem(),
                'to' => $financings->lastItem()
            ],
            'financings' => $financings,
            'totalPendingFinancings' => $totalPendingFinancings
        ], 'Result OK');
    }

    /**
     * Get Financing
     */
    public function getFinancingById(Request $request)
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
        }

        $user_id = $data["user_id"];
        $verify = $this->verifySession($user_id);

        if (!$verify["bool"]) {
            return $this->sendError($verify["msj"], ['no result'], 422);
        }

        $financing = Financing::select(
            'f.id as financing_id',
            'f.total_value as financing_total_value',
            'f.balance as financing_balance',
            'f.state as financing_state',
            'f.rejection_detail as financing_rejection_detail',
            'fd.date_init as financing_detail_date_init',
            'fd.date_end as financing_detail_date_end',
            'fd.net_value as financing_detail_net_value',
            'fd.interest as financing_detail_utility',
            'fd.fines as financing_detail_fines',
            'fd.daily_quota as financing_detail_daily_quota',
            'fd.quotas as total_quotes as financing_detail_total_quotes',
            'fd.discounts as financing_detail_discounts',
            'fr.description as financing_request_description',
            'fr.state as financing_request_state',
            'frec.days as frecuency_days',
            'p.name as product_name',
            'br.name as branch_office_name',
            'pe.names as collector_names',
            'pe.surnames as collector_surnames',
            'pe2.id as customer_id',
            'pe2.names as customer_names',
            'pe2.surnames as customer_surnames',
            'pe2.cell_phone_number as customer_cell_phone_number',
            'l.address as customer_address',
            'ar.name as area_name_customer_address',
            'c.name as city_name_customer_address',
            'pe3.id as deptor_id',
            'pe3.names as deptor_names',
            'pe3.surnames as deptor_surnames',
            'pe3.cell_phone_number as deptor_cell_phone_number',
            'l2.address as deptor_address',
            'ar2.name as area_name_deptor_address',
            'c2.name as city_name_deptor_address'
        )
            ->from('financings as f')
            ->join('financing_details as fd', 'f.id', '=', 'fd.id')
            ->join('financing_requests as fr', 'f.financing_request_id', '=', 'fr.id')
            ->join('products as p', 'fr.product_id', '=', 'p.id')
            ->join('branch_offices as br', 'f.branch_office_id', '=', 'br.id')
            ->join('frecuencies as frec', 'fd.frecuency_id', '=', 'frec.id')
            ->join('persons as pe', 'f.collector_id', '=', 'pe.id')
            ->join('persons as pe2', 'fr.client_id', '=', 'pe2.id')
            ->join('locations as l', 'pe2.location_id', '=', 'l.id')
            ->join('areas as ar', 'l.area_id', '=', 'ar.id')
            ->join('cities as c', 'ar.city_id', '=', 'c.id')
            ->join('persons as pe3', 'fr.deptor_id', '=', 'pe3.id')
            ->join('locations as l2', 'pe3.location_id', '=', 'l2.id')
            ->join('areas as ar2', 'l2.area_id', '=', 'ar2.id')
            ->join('cities as c2', 'ar2.city_id', '=', 'c2.id')
            ->where('f.id', '=', $data["financing_id"])
            ->limit(1)
            ->get();

        if (count($financing) != 0) {
            $payments = Payment::where('financing_id', '=', $financing[0]->financing_id)->get();
        } else {
            $financing = [];
            $payments = [];
        }

        return $this->sendResponse([
            'financing' => [
                'details' => $financing,
                'payments' => $payments
            ],
        ], 'Result OK');
    }

    /**
     * Display a listing of the cunrrent resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPendingFinancings(Request $request)
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

        $data = array(
            'f.id as id_financing',
            'f.date_financing',
            'f.total_value',
            'f.balance',
            'f.state',
            'f.exception_state',
            'f.expiration_exception_state',
            'fd.*',
            'frec.days',
            'pe1.names as names_p1',
            'pe1.surnames as surnames_p1',
            'l.address as customer_address',
            'ar.name as area_name',
            'pe2.names as names_p2',
            'pe2.surnames as surnames_p2'
        );

        $financings = Financing::select($data)->from('financings as f')
            ->join('financing_details as fd', 'fd.id', '=', 'f.id')
            ->join('frecuencies as frec', 'fd.frecuency_id', '=', 'frec.id')
            ->join('financing_requests as fr', 'f.financing_request_id', '=', 'fr.id')
            ->join('persons as pe1', 'fr.client_id', '=', 'pe1.id')
            ->join('locations as l', 'pe1.location_id', '=', 'l.id')
            ->join('areas as ar', 'l.area_id', '=', 'ar.id')
            ->join('persons as pe2', 'fr.deptor_id', '=', 'pe2.id')
            ->where([
                ['f.state', '=', 'pending'],
                ['f.total_value', '>', 0],
                ['f.collector_id', '=', $user_id]
            ])
            ->orderBy('f.id', 'desc')
            ->get();

        $date = date('Y-m-d');
        $data_financings = array();
        $i = 0;

        foreach ($financings as $row) {
            $total_days = $row->quotas;

            $date_financing = new DateTime($row->date_init);
            $current_date = new DateTime(date('Y-m-d'));
            $diff = $date_financing->diff($current_date);
            $current_days = $diff->days;
            $frecuency = $row->days;
            $daily_quota = $row->daily_quota;

            $extended = $current_days - $total_days;

            if ($extended < 0) {
                $extended = 0;
            }

            $expected_value = $daily_quota * $current_days;
            $total_payments = $row->total_value - $row->balance;

            $expected_paid_days = ceil(($expected_value * $total_days) / $row->total_value);
            $paid_days = ceil(($total_payments * $total_days) / $row->total_value);

            $add = false;

            if ((($expected_paid_days - $paid_days) > $frecuency) || ($frecuency == 1)) {

                if ($row->exception_state) {
                    if ($row->expiration_exception_state <= $date) {
                        $row->exception_state = 0;
                        $row->expiration_exception_state = null;
                        $row->save();
                        $add = true;
                    }
                } else if ($frecuency == 1 && ($expected_value < $total_payments)) {
                    $add = false;
                } else {
                    $add = true;
                }
            } else if (($current_days % $frecuency) == 0) {
                $add = true;
            }

            if ($add) {
                $i++;
                $data_financings[$row->id_financing] = [
                    'id_temp' => $i,
                    'date_init' => $row->date_init,
                    'date_end' => $row->date_end,
                    'total_value' => $row->total_value,
                    'balance' => $row->balance,
                    'customer' => $row->names_p1 . ' ' . $row->surnames_p1,
                    'deptor' => $row->names_p2 . ' ' . $row->surnames_p2,
                    'extended' => $extended,
                    'expected_value' => $expected_value,
                    'total_payments' => $total_payments,
                    'expected_paid_days' => $expected_paid_days,
                    'paid_days' => $paid_days,
                    'total_days' => $total_days,
                    'current_days' => $current_days,
                    'back_days' => $expected_paid_days - $paid_days,
                    'ubication' => $row->customer_address . ', ' . $row->area_name,
                    'state' => $row->state
                ];
            }
        }

        return $this->sendResponse([
            'financings' => [
                'all' => $financings,
                'currents' => $data_financings
            ],
        ], 'Result OK');
    }

    /**
     * Refinance
     */
    public function refinance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'financing_id' => 'required',
            'financing_request_amount' => 'required',
            'financing_request_real_amount' => 'required',
            'financing_request_date_financing_requests' => 'required',
            'financing_request_description' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validacion.', $validator->errors(), 409);
        }

        $data = $request->post();

        $user = User::where('id', '=', $data["user_id"])->get();

        if (count($user) == 0) {
            return $this->sendError('Usuario no encontrado.', ['no result'], 422);
        }

        $user_id = $data["user_id"];
        $verify = $this->verifySession($user_id);

        if (!$verify["bool"]) {
            return $this->sendError($verify["msj"], ['no result'], 422);
        }

        $errors = array();
        $bool = false;

        if ($data["financing_request_amount"] <= 0 || $data["financing_request_real_amount"] <= 0) {
            $errors[] = 'El valor para el nuevo financiamiento no puede ser menor o igual a 0';
            $bool = true;
        }

        $financing = Financing::findOrFail($data["financing_id"]);

        if ($financing->state == 'refinanced') {
            $errors[] = 'El financiamiento seleccionado ya ha sido refinanciado';
            $bool = true;
        }

        if (!$bool) {
            DB::beginTransaction();

            try {
                $financingRequest = FinancingRequest::findOrFail($financing->financing_request_id);

                $financingRequest_2 = new FinancingRequest();
                $financingRequest_2->product_id = $financingRequest->product_id;
                $financingRequest_2->client_id = $financingRequest->client_id;
                $financingRequest_2->deptor_id = $financingRequest->deptor_id;
                $financingRequest_2->adviser_id = $financingRequest->adviser_id;
                $financingRequest_2->date_financing_requests = $data["financing_request_date_financing_requests"];
                $financingRequest_2->description = $data["financing_request_description"];
                $financingRequest_2->amount = $data["financing_request_amount"];
                $financingRequest_2->refinancing_request = 1;
                $financingRequest_2->amount_refinancing_request = $data["financing_request_real_amount"];
                $financingRequest_2->quantity = 1;
                $financingRequest_2->save();

                $financing->state = 'refinanced';
                $financing->financing_request_refinanced_id = $financingRequest_2->id;
                $financing->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                $errors[] = 'Hubo un error al actualizar los registros en la base de datos, por favor intente nuevamente o comunicate con soporte ' . $e->getMessage();
                $bool = true;
            }
        }

        if ($bool) {
            return $this->sendError('Errores de validacion.', json_encode($errors), 422);
        }

        return $this->sendResponse(['success'], 'Este registro ha sido refinanciado satisfactoriamente');
    }

    /**
     * Postpone
     */
    public function postpone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'financing_expiration_exception_state' => 'required',
            'financing_id' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validacion.', $validator->errors(), 409);
        }

        $data = $request->post();

        $user = User::where('id', '=', $data["user_id"])->get();

        if (count($user) == 0) {
            return $this->sendError('Usuario no encontrado.', ['no result'], 422);
        }

        $user_id = $data["user_id"];
        $verify = $this->verifySession($user_id);

        if (!$verify["bool"]) {
            return $this->sendError($verify["msj"], ['no result'], 422);
        }

        if ($data["financing_expiration_exception_state"] <= date('Y-m-d')) {
            return $this->sendError('Error de validacion.', ['La fecha de reagendamiento no puede ser menor o igual a la fecha de hoy'], 409);
        }

        $financing = Financing::findOrFail($data["financing_id"]);

        if ($financing->state != 'pending') {
            return $this->sendError('Error de validacion.', ['Este financiamiento no se encuentra en estado pendiente'], 409);
        }

        $financing->exception_state = 1;
        $financing->expiration_exception_state = $data["financing_expiration_exception_state"];
        $financing->save();

        return $this->sendResponse(['success'], 'El financiamiento ha sido reagendado para el: ' . $data["financing_expiration_exception_state"]);
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
