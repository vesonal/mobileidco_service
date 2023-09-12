<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\Organization;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use DB;
use Otp;
use App\ApiDetail;
use Carbon\Carbon;
use App\Registration;
use Ichtrojan\Otp\Models\Otp as Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Pages;
use App\Version;
use App\ApiSetting;
use App\Http\Resources\ApiDetailResource;
use App\Http\Resources\ApiDetailCollection;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->guard = "api"; // add
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
 

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function login(Request $request){
       // dd($request->all());
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        //Request is validated
        //Crean token
        
        try {
            if (! $token = auth('api')->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
        return $credentials;
            return response()->json([
                    'success' => false,
                    'message' => 'Could not create token.',
                ], 500);
        }
    
        //Token created, return with success response and jwt token
        return response()->json([
            'status' => true,
            'data' => ["bearer_token"=>$token],
            'message'=>'successfully login'
        ]);
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function create(Request $request)
    {
       // dd($request->all());
       

        if (empty($request->client_id))
        {
            return response()
                ->json(['message' => 'No External reference has been given', 'status' => false]);
        }
        if (empty($request->client_name))
        {
            return response()
                ->json(['message' => 'Device name cannot not be empty', 'status' => false]);
        }
        if (strlen($request->client_id)< 5)
        {
            return response()
                ->json(['message' => 'External reference cannot not be lesser than 6 charcter', 'status' => false]);
        }
        
        $client_id = $request->client_id;
        $client_name = $request->client_name;
        $token = $request->header('Authorization');
        $datas['api_url'] = 'api/registration';
        $datas['payload'] = $request->all();
        $datas['status'] = 2;
        $this->trackApiDetail($token,$datas);
        if (empty($request->activated_device))
        {
            $isClient = $this->IsCLientExist($client_id);
            if ($isClient)
            {
                return response()->json(['message' => 'device already exist,- press Activate device again to override', 'is_activated' => 1]);
            }
        }
        $code = $this->generateOtp($client_id);
      //  return response()->json(['activation_code' => $code, 'message' => 'activation code', 'status' => 200]);

        $url = getenv('MIDserver_ADMIN_URL') . '/clients';
        //  "grant_types": ["authorization_code","refresh_token"],
        $data = '{
            "client_id": "' . $client_id . '",
            "client_name": "' . $client_name . '",
            "client_secret": "' . $client_id . '",
            "redirect_uris": ["http://127.0.0.1:5555/callback"],
             "grant_types": ["client_credentials"],
            "response_types": ["code"],
            "scope": "offline_access offline openid",
            "audience": [],
            "owner": "",
            "policy_uri": "",
            "allowed_cors_origins": [],
            "tos_uri": "",
            "client_uri": "",
            "logo_uri": "",
            "contacts": [],
            "client_secret_expires_at": 0,
            "subject_type": "public",
            "jwks": {},
            "token_endpoint_auth_method": "client_secret_basic",
            "userinfo_signed_response_alg": "none",
            "created_at": "2021-11-19T10:07:02Z",
            "updated_at": "2021-11-19T10:07:01.850301Z",
            "metadata": {}
          }';
        //for oauth
        // $data = '{
        //   "client_id": "'.$client_id.'",
        //   "client_name": "'.$client_name.'",
        //   "client_secret": "'.$client_id.'",
        //   "redirect_uris": ["http://127.0.0.1:5555/callback"],
        //    "grant_types": ["authorization_code","refresh_token"],
        //   "response_types": ["code"],
        //   "scope": "offline_access offline openid",
        //   "audience": [],
        //   "owner": "",
        //   "policy_uri": "",
        //   "allowed_cors_origins": [],
        //   "tos_uri": "",
        //   "client_uri": "",
        //   "logo_uri": "",
        //   "contacts": [],
        //   "client_secret_expires_at": 0,
        //   "subject_type": "public",
        //   "jwks": {},
        //   "token_endpoint_auth_method": "client_secret_post",
        //   "userinfo_signed_response_alg": "none",
        //   "created_at": "2021-11-19T10:07:02Z",
        //   "updated_at": "2021-11-19T10:07:01.850301Z",
        //   "metadata": {}
        // }';
        $postdata = $data;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $res = json_decode($result, true);
        //print_r ($res);die;
        if ($res)
        {
            $client = Registration::where('device_id', $client_id)->first();
            $code = $this->generateOtp($client_id);
            //in the case of array only $data(in use)else use $code 
            $data = ['activation_code'=>$code];
            if ($httpcode == 201)
            {
                // $res = json_decode($result,true);
                $client_id = $res['client_id'];
                $client_name = $res['client_name'];
                if ($client === null)
                {
                    $values = array(
                        'device_id' => $client_id,
                        'device_name' => $client_name,
                        'activation_code' => $code,
                        'status' => 0
                    );
                    $query = DB::table('registrations')->insert($values);
                    if ($query)
                    {
                        return response()->json(['data' => $data, 'message' => 'activation code', 'status' => true]);
                    }
                }
                else
                {
                    $client->activation_code = $code;
                    $client->save();
                    return response()
                        ->json(['data' => $data, 'message' => 'activation code', 'status' => true]);
                }

            }
            else
            {
                //  if($client->status==0){
                $client->activation_code = $code;
                $client->save();
                return response()
                    ->json(['data' => $data, 'message' => 'activation code', 'status' => true]);
                //   }
                
            }
            // else{
            // return response()->json(['message'=>'Unable to insert or update resource because a resource with that value exists already','status'=>500], 500);
            // }
            
        }
        return response()->json(['message' => 'Unable to insert or update resource because a resource with that value exists already', 'status' => false]);

    }

    public function IsCLientExist($client_id)
    {

        // $client = new Client();
        // $response = $client->get("http://127.0.0.1:4445/clients/$client_id");
        // $response = (string) $response->getBody();
        $response = Registration::where(['device_id' => $client_id, 'status' => 1])->first();

        if ($response)
        {
            return 1;

        }
        return;
    }

    public function getAvailableDevice(Request $request,$client_id)
    {
        $response = Registration::where(['device_id' => $client_id, 'status' => 1])->first();
        if ($response)
        {
            $token = $request->header('Authorization');
            // $this->trackApiDetail($token,'api/client');
            $data = ['available_device'=>$response->device_name];
           return response()->json(['message' => 'Account exist', 'status' => true, 'data' => $data], 200);
        }
        else
        {
            return response()
                ->json(['message' => 'Account donot exist', 'status' => false,'data'=>[]]);
        }
    }

    function trackApiDetail($token,$data)
    {
        // dd($data);
        $baseurl = getenv('APP_URL');
        $url = $baseurl."/api/user";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
           "Accept: application/json",
           "Authorization:".$token,
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        $resp = json_decode($resp);
        curl_close($curl);
        $api = new ApiDetail;
        $api->org_id = $resp?$resp->id:'';
        $api->api_url = $data['api_url']?$data['api_url']:'' ;
        $api->client_id = $data['payload']?$data['payload']['client_id']:'' ;
        $api->payload = json_encode($data['payload']) ;
        $api->mode = 'api';
        $api->status = $data['status']?$data['status']:'' ;
        if($api->save()){
            return true;
        }; 
    }


    public function generateOtp($client_id)
    {
        $otp = new Otp;
        $otp_token = $otp->generate($client_id, 6, 2);
        return $otp_token->token;
    }

    public function verifyActivationCode(Request $request)
    {
        $authorization = $request->header('MobileIDAuthorization');
        $data['authorization'] = $authorization;
        $authorization = $this->isAuthorizeApiToken($data);
        if($authorization==false){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        if (empty($request->otp))
        {
            return response()
                ->json(['message' => 'OTP required', 'status' => false]);
        }
        if (empty($request->device_id))
        {
            return response()
                ->json(['message' => 'Device id required', 'status' => false]);
        }
        $token = $request->otp;
        //$identifier = $request->client_id;
       // $otp = Model::where('identifier', 'like', '%' . $identifier . '%')->where('token', $token)->first();
        $otp = Model::where('token', $token)->first();
      //  dd($otp);
        if ($otp == null)
        {
            return response()
                ->json(['message' => 'OTP does not exist', 'status' => false]);
        }
        else
        {
            if ($otp->valid == 1)
            {
                $carbon = new Carbon;
                $now = $carbon->now();
                $validity = $otp
                    ->created_at
                    ->addMinutes($otp->validity);
                if (strtotime($validity) < strtotime($now))
                {
                    $otp->valid = 0;
                    $otp->save();
                    return response()
                ->json(['message' => 'OTP Expired', 'status' => false]);
                }
                else
                {
                    $otp->valid = 0;
                    $otp->save();
                    $identifier = $otp->identifier;
                    $client = Registration::where('device_id',$identifier)->first();
                    $client->status = 0;   //if otp is valid then after pin validate status get 1
                    $client->fcm_device_id = $request->device_id;
                    $client->save();
                    // $trans =  ApiDetail::where(['client_id'=>$identifier,'api_url'=>'api/registration'])->latest()->first();
                    // $trans->status = 1;
                    // $trans->save();
                    $datas['api_url'] = 'api/verifyOtp';
                    $datas['payload'] = array('client_id'=>$identifier);
                    $datas['status'] = 1;
                    $this->trackApiDetail($token,$datas);
                  //  $res= $this->clientRegistration($identifier,$identifier);
                  return response()
                ->json(['message' => 'OTP is valid,Registration done', 'status' => true,'data'=>$client]);
                }
            }
            else
            {
                return response()->json(['status' => false, 'message' => 'OTP is not valid',]);
            }
        }
    }

    public function createUserToken(Request $request)
    {   
        if (empty($request->client_id))
        {
            return response()
                ->json(['message' => 'No device  found for specified account ', 'status' => false]);
        }
        $token = $request->header('Authorization');
        $datas['api_url'] = 'api/authentication';
        $datas['payload'] = $request->all();
        $datas['status'] = 2;
        $this->trackApiDetail($token,$datas);
        $isClientexist = $this->IsCLientExist($request->client_id);
        if($isClientexist!=1){
        return response() ->json(['message' => 'Device is not registered', 'status' => false]);
        }
        $client_id = $request->client_id;
        $password = $request->client_id;
        $isClientexist = $this->IsCLientExist($request->client_id);
        $url = getenv('MIDserver_PUBLIC_URL') . '/oauth2/token';
        $data = ["grant_type" => "client_credentials"];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        //    print_r ($result);die;
        if (!empty($result))
        {
            $client = Registration::where('device_id', $client_id)->first();
            $client->token = json_decode($result)->access_token;
            // $client->auth_status = 1;
            $client->save();
            $instro = $this->instrospectToken(json_decode($result)->access_token);
            $notification_id = $client->fcm_device_id;
            $message = ($request->push_payload)?$request->push_payload:'';
            $screen = 'authenticate';
            $fcm = $this->send_notification_FCM($notification_id,'Authentication',$message,$screen);
            $fcm = json_decode($fcm,true);
            // dd($fcm);
            if($fcm["success"]==1){
                return response()->json(['message' => 'Authorization token ', 'status' => true, 'data' => json_decode($result) ]);
            }
            return response()
                ->json(['message' => 'Authorization token ', 'status' => false, 'data' => [] ]);
            //  print_r($instro);die;
        }
        return response()->json(['message' => 'Account donot exist', 'status' => false]);
    }

    public function instrospectToken($token)
    {
        $data = ["token" => $token];
        $url = getenv('MIDserver_Admin_URL') . '/oauth2/introspect';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $result;
    }

    public function convertPayloasJwt()
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        // Create token payload as a JSON string
        $payload = json_encode(['user_id' => 'beauty']);
        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        // echo $jwt;
        //decode jwt
        $token = $jwt;
        print_r(json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token) [1])))));
    }

    public function consentSign(Request $request)
    {
        //print_r($request->all());
        if (empty($request->client_id))
        {
            return response()
                ->json(['message' => 'No Client  found for specified account ', 'status' => false]);
        }
        if (empty($request->device_id))
        {
            return response()
                ->json(['message' => 'No device  found for specified account ', 'status' => false]);
        }
        if (empty($request->preContextTitle))
        {
            return response()
                ->json(['message' => 'content text cannot not be empty', 'status' => false]);
        }
        $isClientexist = $this->IsCLientExist($request->client_id);
        if($isClientexist!=1){
        return response()->json(['message' => 'Device is not registered', 'status' => false]);
        }
        $token = $request->header('Authorization');
        $datas['api_url'] = 'api/consentSign';
        $datas['payload'] = $request->all();
        $datas['status'] = 2;
        $this->trackApiDetail($token,$datas);
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $pushPayload  = $request->pushPayload?$request->pushPayload:'';
        $preContextContent  = $request->preContextContent?$request->preContextContent:'';
        // Create token payload as a JSON string
        $payload = json_encode(['client_id' => $request->client_id, 'device_id' => $request->device_id, 'push_payload' => $pushPayload,'preContextTitle' =>$request->preContextTitle,'preContextContent'=>$preContextContent]);
        
        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        // $jwt = ["jwt"=>$jwt];
        $notification_id = $this->getdeviceToken($request->client_id);
        $message = ($request->preContextTitle)?$request->preContextTitle:'';
        $screen  = 'consentSign';
        $fcm = $this->send_notification_FCM($notification_id,'Consent Signature',$jwt,$screen);
        $fcm = json_decode($fcm,true);
        // dd($fcm);
        if($fcm["success"]==1){
             return response()->json(['message' => 'Authorization token ', 'status' => 'success', 'data' => $jwt ]);   
        }
            return response()
                ->json(['message' => 'Authorization token ', 'status' => 'error', 'data' => [] ]);
        // return response()->json(['message'=>'consent sign','data' => $jwt, 'status' => true]);
       // return $jwt;
    }

   


    function send_notification_FCM($notification_id, $title, $message,$screen) 
    {
        // $accesstoken = env('FCM_KEY');
        $config = ApiSetting::first();
        $accesstoken = $config->fcm_key;
        // $accesstoken = 'key=AAAA8T6rNkU:APA91bH-s24bYPSYJkMLexCDU4FoYTTwa-VrkQAUTDE-xK9BJxD2-P5K2ScxFuPV2QMADqvv5IJuvAmY3tQPSJNLm3zRTFQ5of1dcWs2zLFGPz-OQQy9fL-c8cxcDWDcAp-hWI56S_YG';
        $URL = 'https://fcm.googleapis.com/fcm/send';
        if($screen=='consentSign'){
            $token = $message;
            $datas = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));
            $preContextTitle = $datas->preContextTitle;
            $preContextContent = $datas->preContextContent?$datas->preContextContent:'';
            $post_data = '{
                "to" : "' . $notification_id . '",
                "data" : {
                  "body" : "",
                  "title" : "'.$title.'",
                  "type" : "android",
                  "message" : "'.$preContextTitle.'",
                  "preContextContent":"'.$preContextContent.'",
                  "screenType":"'.$screen.'"
                },
                "notification" : {
                     "body" : "'.$preContextTitle.'",
                     "title" : "' . $title . '",
                     "type" : "android",
                     "message" : "' . $preContextTitle . '",
                     "icon" : "new",
                     "sound" : "default"
                    },
         
               }'; 
        }else{
        $post_data = '{
            "to" : "' . $notification_id . '",
            "data" : {
              "body" : "",
              "title" : "'.$title.'",
              "type" : "android",
              "message" : "Authentication ",
              "screenType":"'.$screen.'"
            },
            "notification" : {
                 "body" : "'.$message.'",
                 "title" : "' . $title . '",
                 "type" : "android",
                 "message" : "' . $message . '",
                 "icon" : "new",
                 "sound" : "default"
                },
     
           }';
        }
        $crl = curl_init();
        $headr = array();
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: ' . $accesstoken;
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($crl, CURLOPT_URL, $URL);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
     
        $rest = curl_exec($crl);
        return $rest;
        if ($rest === false) {
            // throw new Exception('Curl error: ' . curl_error($crl));
            //print_r('Curl error: ' . curl_error($crl));
            $result_noti = 0;
        } else {
            $result_noti = 1;
        }
        //curl_close($crl);
        //print_r($result_noti);die;
        return $result_noti;
    }


    public function checkStatus(Request $request)
    {
        if ($request->status == 'accept')
        {
            $client = Registration::where('device_id', $request->client_id)
                ->first();
            $client->auth_status = 1;
            $client->save();
            return response()
                ->json(['message' => 'success', 'status' => 'success']);
        }
        return 'Pending';
    }

     public function authorizePayment(Request $request)
     {
        //print_r($request->all());
         $token = $request->header('Authorization');

        if (empty($request->client_id))
        {
            return response()
                ->json(['message' => 'No Client  found for specified account ', 'status' => false]);
        }
        if (empty($request->device_id))
        {
            return response()
                ->json(['message' => 'No device  found for specified account ', 'status' => false]);
        }
        if (empty($request->preContextTitle))
        {
            return response()
                ->json(['message' => 'Payment information cannot not be empty', 'status' => false]);
        }
        // dd($request->all());
        $user = auth::user();
        $datas['api_url'] = 'api/authorizePayment';
        $datas['payload'] = $request->all();
        $datas['status'] = 2 ;
        $this->trackApiDetail($token,$datas);
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        // Create token payload as a JSON string
        $payload = json_encode(['client_id' => $request->client_id, 'device_id' => $request->device_id, 'content_text' => $request->preContextTitle]);
        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        // return $jwt;
        $notification_id = $this->getdeviceToken($request->client_id);
        $message = ($request->preContextTitle)?$request->preContextTitle:'';
        // $message = base64_decode($message);
        $screen  = 'PaymentAuthorization';
        $fcm = $this->send_notification_FCM($notification_id,'Payment Authorization',$message,$screen);
        $fcm = json_decode($fcm,true);
        // dd($fcm);
        if($fcm["success"]==1){
             return response()
                ->json(['message' => 'Payment Authorization token ', 'status' => true, 'data' => $jwt ]);   
        }
            return response()
                ->json(['message' => 'oops!something went wrong', 'status' =>false, 'data' => [] ]);
       // return $jwt;
    }

    public function authenticateStatus(Request $request){
       $trans =  ApiDetail::where('client_id',$request->client_id)->latest()->first();
       $trans->status = $request->status;
       $trans->save();
    }

    public function aboutUs(Request $request){
        $authorization = $request->header('MobileIDAuthorization');
        $data['authorization'] = $authorization;
        $authorization = $this->isAuthorizeApiToken($data);
        if($authorization==true){
            $result = Pages::where('pagename','about_us')->first();
            return response()->json(['message' => 'about us', 'status' => true, 'data' => json_decode($result) ]);
        } else{
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
            }
    }

    public function getConfigurations(Request $request){
        $authorization = $request->header('MobileIDAuthorization');
        $data['authorization'] = $authorization;
        $authorization = $this->isAuthorizeApiToken($data);
        if($authorization==true){
            $version  = Version::get();
            return response()->json(['message' => 'getconfiguration', 'status' => true, 'data' => json_decode($version) ]);
        }
        else{
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
    }

    public function isAuthorizeApiToken($data){
       $apiToken = $data["authorization"];
       $config = ApiSetting::first();
       $api_key = $config->api_key;
    //    $api_key = getenv('SECRET_API_KEY');
        if(empty($apiToken)){
            return false;
        }
        elseif($apiToken==$api_key){
            return true;
        }
        else{
            return false;
        }
    }

    public function RegisterCheckStatus(Request $request){
        $authorization = $request->header('MobileIDAuthorization');
        $data['authorization'] = $authorization;
        $authorization = $this->isAuthorizeApiToken($data);
        if($authorization!=true){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        $trans =  ApiDetail::where(['client_id'=>$request->client_id,'api_url'=>'api/registration'])->latest()->first();
        if($trans->status==1){
            return response()->json(['message' => 'Registration is done,Client is registered now.', 'status' => true ]);
        }
            return response()->json(['message' => 'PENDING', 'status' => false ]);
    }

    public function AuthenticateCheckStatus(Request $request){
        if (empty($request->client_id))
        {
            return response()->json(['message' => ' ClientId should not be empty ', 'status' => false]);
        }
        $authorization = $request->header('MobileIDAuthorization');
        $data['authorization'] = $authorization;
        $authorization = $this->isAuthorizeApiToken($data); 
        if($authorization!=true){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        $isClientexist = $this->IsCLientExist($request->client_id);
        if($isClientexist!=1){
            return response()->json(['message' => 'Device is not registered', 'status' => false]);
        }
        $trans =  ApiDetail::where(['client_id'=>$request->client_id,'api_url'=>'api/authentication'])->latest()->first();
        if($trans->status==1){
            return response()->json(['message' => 'Their is no cached payload with given key', 'status' => true ]);
        }
        return response()->json(['message' => 'PENDING', 'status' => false ]);
    }


    public function PaymentCheckStatus(Request $request){
        $authorization = $request->header('MobileIDAuthorization');
        $data['authorization'] = $authorization;
        $authorization = $this->isAuthorizeApiToken($data); 
        if($authorization!=true){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        if (empty($request->client_id))
        {
            return response()->json(['message' => ' ClientId should not be empty ', 'status' => false]);
        }
        $isClientexist = $this->IsCLientExist($request->client_id);
        if($isClientexist!=1){
            return response()->json(['message' => 'Device is not registered', 'status' => false]);
        }
        $trans =  ApiDetail::where(['client_id'=>$request->client_id,'api_url'=>'api/authorizepayment'])->latest()->first();

        if($trans->status==1){
            return response()->json(['message' => 'Their is no cached payload with given key', 'status' => true ]);

        }
        elseif($trans->status==3){
            return response()->json(['message' => 'The Resource Owner did not complete the login.MOBILEID_AUTHENTICATION_FAILED:CANCELLED_BY_DEVICE', 'status' => false ]);
        }
        else{
            return response()->json(['message' => 'PENDING', 'status' => false ]);
        }
    }

    public function ConsentCheckStatus(Request $request){
        $authorization = $request->header('MobileIDAuthorization');
        $data['authorization'] = $authorization;
        $authorization = $this->isAuthorizeApiToken($data); 
        if($authorization!=true){
          return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        if (empty($request->client_id))
        {
            return response()->json(['message' => ' ClientId should not be empty ', 'status' => false]);
        }
        $isClientexist = $this->IsCLientExist($request->client_id);
        if($isClientexist!=1){
            return response()->json(['message' => 'Device is not registered', 'status' => false]);
        }
        $trans =  ApiDetail::where(['client_id'=>$request->client_id,'api_url'=>'api/consentSign'])->latest()->first();
        if($trans->status==1){
        return response()->json(['message' => 'COMPLETED', 'status' => true ]);
        }
        elseif($trans->status==3){
            return response()->json(['message' => 'The Resource Owner did not complete the login.MOBILEID_AUTHENTICATION_FAILED:CANCELLED_BY_DEVICE', 'status' => false ]);
        }
        else{
            return response()->json(['message' => 'PENDING', 'status' => false ]);
        }

    }
    public function AuthenticationUpdateStatus(Request $request)
    {
        $authorization = $request->header('MobileIDAuthorization');
        $data['authorization'] = $authorization;
        $authorization = $this->isAuthorizeApiToken($data); 
        if($authorization!=true){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        if (empty($request->pin))
        {
            return response()->json(['message' => 'device pin should not be empty', 'status' => false]);
        }
        if (empty($request->device_token))
        {
            return response()->json(['message' => 'No Client found for specified account ', 'status' => false]);
        }
        if(is_numeric($request->pin)){
            if(strlen($request->pin)<=3 && strlen($request->pin)>=5){
            return response()->json(['message'=>'pin should be of only 4 digit','status'=>false]);
            }
        }else{
            return response()->json(['message'=>'pin should be only numeric','status'=>false]);
        }
        $isClientExist =  Registration::where(['fcm_device_id'=>$request->device_token,'status'=>1])->first();
        // dd($isClientExist);
        if(!$isClientExist){
            return response()->json(['message' => 'Device is not registered', 'status' => false]);
        }
        if($request->pin!=$isClientExist->device_pin){
            return response()->json(['message' => 'pin is not valid', 'status' => false]);
        }

        $trans =  ApiDetail::where(['client_id'=>$isClientExist->device_id,'api_url'=>'api/authentication'])->latest()->first();
        $trans->status = 1;
        $trans->latitute = isset($request->latitute)?$request->latitute:'';
        $trans->longitute = isset($request->longitude)?$request->longitude:'';
        $trans->deviceName = isset($request->deviceName)?$request->deviceName:'';
        $trans->deviceBrand = isset($request->deviceBrand)?$request->deviceBrand:'';
        $trans->save();
        //for update auth in registration table
        $isClientExist->auth_status = 1;
        $isClientExist->save();
        return response()
        ->json(['message' => 'Authenticated successfully', 'status' => true]);
    } 
    
    function getdeviceToken($client_id){
        $client = Registration::where('device_id', $client_id)->first();
        return  $client->fcm_device_id;
    }
    
    public function PaymentUpdateStatus(Request $request)
    {
        $authorization = $request->header('MobileIDAuthorization');
        $data['authorization'] = $authorization;
        $authorization = $this->isAuthorizeApiToken($data); 
        //authentication check
        if($authorization!=true){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        if (empty($request->status))
        {
            return response()->json(['message' => 'status should not be empty', 'status' => false]);
        }
        if (empty($request->device_token))
        {
            return response()->json(['message' => 'No Client found for specified account ', 'status' => false]);
        }
        $isClientExist =  Registration::where(['fcm_device_id'=>$request->device_token,'status'=>1])->first();
        if(!$isClientExist){
            return response()->json(['message' => 'Device is not registered', 'status' => false]);
        }
        if($request->status == 'yes')
        {
            if(!empty($request->pin)){
                if(strlen($request->pin)<=3 && strlen($request->pin)>=5){
                    return response()->json(['message'=>'pin should be of only 4 digit','status'=>false]);
                }
                if($request->pin!=$isClientExist->device_pin){
                    return response()->json(['message' => 'pin is not valid', 'status' => false]);
                }
                $trans =  ApiDetail::where(['client_id'=>$isClientExist->device_id,'api_url'=>'api/authorizepayment'])->latest()->first();
                $trans->status = 1;
                $trans->latitute = isset($request->latitute)?$request->latitute:'';
                $trans->longitute = isset($request->longitude)?$request->longitude:'';
                $trans->deviceName = isset($request->deviceName)?$request->deviceName:'';
                $trans->deviceBrand = isset($request->deviceBrand)?$request->deviceBrand:'';
                if($trans->save()){
                    return response()->json(['message' => 'status updated successfully', 'status' => true]);
                }
                return response()->json(['message' => 'status not updated successfully', 'status' => false]);
            }else{
                return response()->json(['message'=>'device pin should not be empty','status'=>false]);
            }
        }
        else if($request->status == 'no'){
        $trans =  ApiDetail::where(['client_id'=>$isClientExist->device_id,'api_url'=>'api/authorizepayment'])->latest()->first();
        $trans->status = 3;
        $trans->latitute = isset($request->latitute)?$request->latitute:'';
        $trans->longitute = isset($request->longitude)?$request->longitude:'';
        $trans->deviceName = isset($request->deviceName)?$request->deviceName:'';
        $trans->deviceBrand = isset($request->deviceBrand)?$request->deviceBrand:'';
        $trans->save();
        return response()->json(['message' => 'status updated successfully', 'status' => true]);
        }
        return response()->json(['message' => 'oops!something went wrong', 'status' => false]);
    } 

    public function ConsentUpdateStatus(Request $request)
    {
        $authorization = $request->header('MobileIDAuthorization');
        $data['authorization'] = $authorization;
        $authorization = $this->isAuthorizeApiToken($data); 
        if($authorization!=true){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        if (empty($request->status))
        {
            return response()->json(['message' => 'status should not be empty', 'status' => false]);
        }
        if (empty($request->device_token))
        {
            return response()->json(['message' => 'No Client found for specified account ', 'status' => false]);
        }
        $isClientExist =  Registration::where(['fcm_device_id'=>$request->device_token,'status'=>1])->orderBY('id','DESC')->first();
        if(!$isClientExist){
            return response()->json(['message' => 'Device is not registered', 'status' => false]);
        }
        if($request->status == 'yes')
        {
            if(!empty($request->pin)){
                if(strlen($request->pin)<=3 && strlen($request->pin)>=5){
                    return response()->json(['message'=>'pin should be of only 4 digit','status'=>false]);
                }
                if($request->pin!=$isClientExist->device_pin){
                    return response()->json(['message' => 'pin is not valid', 'status' => false]);
                }
                $trans =  ApiDetail::where(['client_id'=>$isClientExist->device_id,'api_url'=>'api/consentSign'])->latest()->first();
                $trans->status = 1;
                $trans->latitute = isset($request->latitute)?$request->latitute:'';
                $trans->longitute = isset($request->longitude)?$request->longitude:'';
                $trans->deviceName = isset($request->deviceName)?$request->deviceName:'';
                $trans->deviceBrand = isset($request->deviceBrand)?$request->deviceBrand:'';
                if($trans->save()){
                    return response()->json(['message' => 'status updated successfully', 'status' => true]);
                }
                return response()->json(['message' => 'status not updated successfully', 'status' => false]);
            }else{
                return response()->json(['message'=>'device pin should not be empty','status'=>false]);
            }
        }
        else if($request->status == 'no'){
        $trans =  ApiDetail::where(['client_id'=>$isClientExist->device_id,'api_url'=>'api/consentSign'])->latest()->first();
        $trans->status = 3;
        $trans->latitute = isset($request->latitute)?$request->latitute:'';
        $trans->longitute = isset($request->longitude)?$request->longitude:'';
        $trans->deviceName = isset($request->deviceName)?$request->deviceName:'';
        $trans->deviceBrand = isset($request->deviceBrand)?$request->deviceBrand:'';
        $trans->save();
        return response()->json(['message' => 'status updated successfully', 'status' => true]);
        }
        return response()->json(['message' => 'oops!something went wrong ', 'status' => false]);
    }

    public function deactivateDevice(Request $request){
        $data['authorization'] = $request->header('MobileIDAuthorization');
        $authorization = $this->isAuthorizeApiToken($data);
        if($authorization!=true){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        $isClientExist =  Registration::where(['fcm_device_id'=>$request->device_token,'status'=>1])->orderBY('id','DESC')->first();
        if($isClientExist){
            $isClientExist->status = 0 ;
            $isClientExist->save();
        return response()->json(['message' => 'Device is deactivated', 'status' => true]);
        }
        else{
            return response()->json(['message' => 'No Device is found', 'status' => false]);
        }
    }

    public function SaveregisterPin(Request $request){
        $data['authorization'] = $request->header('MobileIDAuthorization');
        $authorization = $this->isAuthorizeApiToken($data);
        if($authorization!=true){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        if (empty($request->pin))
        {
            return response()
                ->json(['message' => 'pin should not be empty', 'status' => false]);
        }
        if (empty($request->device_token))
        {
            return response()
                ->json(['message' => 'No Client found for specified device token ', 'status' => false]);
        }
        if (empty($request->device_id))
        {
            return response()
                ->json(['message' => 'Device Id should not be empty', 'status' => false]);
        }
        if(is_numeric($request->pin)){
            if(strlen($request->pin)<=3 && strlen($request->pin)>=5){
                return response()->json(['message'=>'pin should be of only 4 digit','status'=>false]);
            }
        }else{
            return response()->json(['message'=>'pin should be only numeric','status'=>false]);
        }
        $client = Registration::where(['fcm_device_id'=> $request->device_token,'device_id'=>$request->device_id])->first();
        if($client){
            $client->device_pin = $request->pin;
            $client->status = 1; // registered user only pin get validate
            $client->device_name = isset($request->deviceName)?$request->deviceName:$client->device_name; // registered user only pin get validate
            $client->save();
            $trans =  ApiDetail::where(['client_id'=>$request->device_id,'api_url'=>'api/registration'])->latest()->first();
            $trans->status = 1;
            $trans->latitute = isset($request->latitute)?$request->latitute:'';
            $trans->longitute = isset($request->longitude)?$request->longitude:'';
            $trans->deviceName = isset($request->deviceName)?$request->deviceName:'';
            $trans->deviceBrand = isset($request->deviceBrand)?$request->deviceBrand:'';
            if($trans->save()){
                return response()->json(['message' => 'Pin registered', 'status' => true]);
            }
            return response()->json(['message' => 'oops,something went wrong!', 'status' => false]);

        }
        return response()->json(['message' => 'No Device is found', 'status' => false]);
    }

    public function getdevicePin(Request $request){
        $data['authorization'] = $request->header('MobileIDAuthorization');
        $authorization = $this->isAuthorizeApiToken($data);
        if($authorization!=true){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        if (empty($request->device_token))
        {
            return response()->json(['message' => 'device token should be required', 'status' => false]);
        }
        $client = Registration::where('fcm_device_id', $request->device_token)->first();
        // dd($client);
        if($client){
            $data = $client->device_pin;
            return response()->json(['message' => 'Pin Details', 'status' => true,'data'=>$data]);
        }
        return response()->json(['message' => 'No Device is found', 'status' => false,'data'=>[]]);
    }

    public function historyDetail(Request $request){
        $data['authorization'] = $request->header('MobileIDAuthorization');
        $authorization = $this->isAuthorizeApiToken($data);
        if($authorization!=true){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        if (empty($request->client_id))
        {
            return response()
                ->json(['message' => 'Client Id should be required', 'status' => false]);
        }
        $data = ApiDetailResource::collection(ApiDetail::whereIn('api_url', array('api/registration','api/authentication','api/consentSign','api/authorizepayment'))->where(['client_id'=>$request->client_id,'status'=>1])->get());

        // $data = ApiDetailResource::collection(ApiDetail::where(['client_id'=>$request->client_id,'status'=>1])->orderBy('id','DESC')->get());
      
        if($data){
            return response()->json(['message' => 'History Details', 'status' => true,'data'=>$data]);
        }
        return response()->json(['message' => 'No history is found', 'status' => false,'data'=>[]]);
    }

    public function updateRegisterPin(Request $request){
        $data['authorization'] = $request->header('MobileIDAuthorization');
        $authorization = $this->isAuthorizeApiToken($data);
        if($authorization!=true){
            return response()->json(['message' => 'Unauthorized.','status'=>false,'data'=>[]], 401);
        }
        if(empty($request->device_id))
        {
            return response()
                ->json(['message' => 'Device Id should be required', 'status' => false]);
        }
        if(empty($request->pin))
        {
            return response()->json(['message' => 'Device Pin should be required', 'status' => false]);
        }
        $device =  Registration::select('id','device_pin','device_id')->where('device_id',$request->device_id)->first();
        $device->device_pin = $request->pin;
        if($device->save()){
          return response()->json(['message' => 'pin successfully updated ', 'status' => true,'data'=>$device]);
        }
          return response()->json(['message' => 'oops!something went wrong', 'status' => false,'data'=>[]]);
    }
    

}
