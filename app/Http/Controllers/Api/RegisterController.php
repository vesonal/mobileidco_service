<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use Session;
use App\Registration;
// use App\Otp;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Ichtrojan\Otp\Models\Otp as Model;
use Otp;
use KubAT\PhpSimple\HtmlDomParser;
use App\ApiDetail;
use Illuminate\Support\Facades\Auth;
use App\ApiSetting;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
         if (empty($request->client_id))
        {
            return response()
                ->json(['message' => 'No External reference has been given', 'status' => 'error']);
        }
        // if (empty($request->client_name))
        // {
        //     return response()
        //         ->json(['message' => 'Device name cannot not be empty', 'status' => 'error']);
        // }
        if (strlen($request->client_id)< 5)
        {
            return response()
                ->json(['message' => 'External reference cannot not be lesser than 6 charcter', 'status' => 'error']);
        }
        $payload_data = $request->all();
        $user = auth::user();
        $datas['org'] = $user;
        $datas['api_url'] = 'api/registration';
        $datas['payload'] = $payload_data;
        $this->trackApiDetail($datas);

        $client_id = $request->client_id;
        $client_name = isset($request->client_name)?$request->client_name:'';
        if (empty($request->activated_device)) {
            $isClient = $this->IsCLientExist($client_id);
            if($isClient){
                 return response()->json(['activation_code'=>'device already exist,- press Activate device again to override','is_activated'=>1]);
            }
        }
        
        $url = getenv('MIDserver_ADMIN_URL').'/clients';
        $data = '{
            "client_id": "'.$client_id.'",
            "client_name": "'.$client_name.'",
            "client_secret": "'.$client_id.'",
            "redirect_uri": ["http://18.168.126.144:5555/callback"],
            "grant_types": ["client_credentials"],
            "response_types": ["code"],
            "scope": "offline_access offline openid",
            "audience": [],
            "owner": "",
            "policy_uri": "",
            "allowed_cors_origins": ["http://18.168.126.144"],
            "tos_uri": "",
            "client_uri": "",
            "logo_uri": "",
            "contacts": [],
            "client_secret_expires_at": 0,
            "subject_type": "public",
            "jwks": {},
            "token_endpoint_auth_method": "client_secret_basic",
            "userinfo_signed_response_alg": "none",
            "created_at": "2021-12-19T10:07:02Z",
            "updated_at": "2021-11-19T10:07:01.850301Z",
            "metadata": {}
          }';

        $postdata = $data;
        $ch = curl_init($url); 
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $res = json_decode($result,true);
        if ($res) {
           $client = Registration::where('device_id',$client_id)->first();
            // print_r($client);die;
           $code = $this->generateOtp($client_id);
           if ($httpcode==201) {
           // $res = json_decode($result,true);
            $client_id = $res['client_id'];
            $client_name = isset($res['client_name'])?$res['client_name']:'';
            if ($client === null) {
                $values = array('device_id' => $client_id,'device_name'=>$client_name,'activation_code'=>$code,'status'=>0);
                $query = DB::table('registrations')->insert($values);
                if ($query) {
                    return response()->json(['activation_code' => $code,'message'=>'activation code','status'=>200]);
                }
            } else{
                    $client->activation_code = $code;
                    $client->save();
                    return response()->json(['activation_code' => $code,'message'=>'activation code','status'=>200]);
            }    
              
            }
            else{
              //  if($client->status==0){
                    $client->activation_code = $code;
                    $client->save();
                    return response()->json(['activation_code' => $code,'message'=>'activation code','status'=>200]);
             //   }

            }
            // else{

               // return response()->json(['message'=>'Unable to insert or update resource because a resource with that value exists already','status'=>500], 500);
            // }
       }
                return response()->json(['message'=>'Unable to insert or update resource because a resource with that value exists already','status'=>500], 500);


        
    }


    public function IsCLientExist($client_id){

            // $client = new Client();
            // $response = $client->get("http://127.0.0.1:4445/clients/$client_id");
            // $response = (string) $response->getBody();
            $response = Registration::where(['device_id'=>$client_id,'status'=>1])->first();

            if ($response) {
                return 1;
               
            }
            return ;
    }




    public function getAvailableDevice($client_id){
        $response = Registration::where(['device_id'=>$client_id,'status'=>1])->first();

            if ($response) {
                return response()->json(['message'=>'Account exist','status'=>'success','data'=>$response->device_name], 200);
            }
            else{
                return response()->json(['message'=>'Account donot exist','status'=>'error']);
            }
    }

    public function verifyActivationCode11(Request $request){

        $otp = new Otp;
        $otpvalue = $request->otp;
        $client_id = $request->client_id;
      
       // $otp = Otp::validate('michael@okoh.co.uk', '282581');
        $res = $otp->validate('".$client_id."','".$otpvalue."');
     
        dd($res);
    }


    public function generateOtp($client_id){
         $otp = new Otp;
         $otp_token = $otp->generate($client_id, 6, 2);
         return $otp_token->token;
    }


public function verifyActivationCode(Request $request)
    {
        if (empty($request->otp))
        {
            return response()
                ->json(['message' => 'OTP required', 'status' => 'error']);
        }
        $token = $request->otp;
        //$identifier = $request->client_id;
       // $otp = Model::where('identifier', 'like', '%' . $identifier . '%')->where('token', $token)->first();
        $otp = Model::where('token', $token)->first();
      //  dd($otp);
        if ($otp == null)
        {
            return response()
                ->json(['message' => 'OTP does not exist', 'status' => 'error']);
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
                ->json(['message' => 'OTP Expired', 'status' => 'error']);
                }
                else
                {
                    $otp->valid = 0;
                    $otp->save();
                    $identifier = $otp->identifier;
                    $client = Registration::where('device_id',$identifier)->first();
                    $client->status = 1;
                    $client->save();
                  //  $res= $this->clientRegistration($identifier,$identifier);
                  return response()
                ->json(['message' => 'OTP is valid', 'status' => 'error']);
                }
            }
            else
            {
                return response()->json(['status' => 'error', 'message' => 'OTP is not valid']);
            }
        }
    }



public function createOauth(Request $request){
  if(empty($request->device_id)){
    return response()->json(['message'=>'Account donot exist','status'=>'error']);
  }
  $url = getenv('MIDserver_ADMIN_URL').'/clients/';
  $public_url = getenv('MIDserver_PUBLIC_URL');
  $IsClientExist = file_get_contents($url.$request->client_id.'');
  if($IsClientExist){
    $client_id = $request->client_id;
    $state = str_shuffle('sdjfgshdfgshdvfbvbsdfhfds');

    //$response = file_get_contents('http://18.168.126.144:4444/oauth2/auth?audience=&client_id=sampleuser&max_age=0&prompt=&redirect_uri=http%3A%2F%2F18.168.126.144%3A5555%2Fcallback&response_type=code&scope=openid+offline&state="'.$state.'"',true);

     $response = file_get_contents('http://18.168.126.144:4444/oauth2/auth?audience=&client_id='.$request->client_id.'&max_age=0&prompt=&redirect_uri=http%3A%2F%2F18.168.126.144%3A5555%2Fcallback&response_type=code&scope=openid&state='.$state.'',true);
      header('Content-Type:text/html; charset=UTF-8');
     // print_r($response);die;
      $dom = new \DOMDocument();
      @$dom->loadHTML($response);
      $xp = new \DOMXpath($dom);
      $nodes = $xp->query('//input[@name="challenge"]');
      $node = $nodes->item(0);
      $login_challenge = $node->getAttribute('value');
      $get_login = file_get_contents(getenv('MIDserver_ADMIN_URL').'/oauth2/auth/requests/login?login_challenge='.$login_challenge.'');
      if (!empty($get_login)) {
         $client = Registration::where('device_id',$client_id)->first();
         $client->auth_status = 1;
         $client->auth_response = $get_login;
         $client->save(); 
         return response()->json(['message'=>'Account exist','status'=>'success','data'=>json_decode($get_login)]);
      }
  }
  else{
      return response()->json(['message'=>'Account donot exist','status'=>'error']);
  }
  
   // print_r($get_login);
    //do login
   // $login_response =  
}




public function createOauthold(Request $request){
  $client_id = $request->client_id;
  $state = str_shuffle('sdjfgshdfgshdvfbvbsdfhfds');
  // $ch = curl_init();
  // curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:4444/oauth2/auth/?audience=&client_id=".$client_id."&max_age=0&prompt=&redirect_uri=http%3A%2F%2F127.0.0.1%3A5555%2Fcallback&response_type=code&scope=openid+offline&state=".$state."");
  // //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  // curl_setopt($ch, CURLOPT_HEADER, 0);
  // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  // $response = curl_exec($ch);
  //print_r($response);
   $response = file_get_contents('http://127.0.0.1:4444/oauth2/auth?audience=&client_id=sampleuser&max_age=0&prompt=&redirect_uri=http%3A%2F%2F127.0.0.1%3A5555%2Fcallback&response_type=code&scope=openid+offline&state='.$state.'',true);
   //print_r($response);
  // $form  = $response;
//   echo '<script type="text/javascript">',
//      'jsfunction();',
//      '</script>'
// ;

   $dom = HtmlDomParser::str_get_html($response);
   $inputs = $dom->getElementsByTagName('input');
   $testInput = null;

foreach ($inputs as $input) {
  print_r($input);
  // if ($input->getAttribute('name') == 'challenge') {
  //   $testInput = $input;
  //   break;
  // }
}

if (!$testInput) {
  exit('There was an error. input[name="test"] could not be found.');
}

// otherwise dump the input!
var_dump($testInput);
 
}

function html_to_obj($html) {
    $dom = new \DOMDocument();
    $dom->loadHTML($html);

    return $this->element_to_obj($dom->documentElement);
}

function element_to_obj($elements) {
    $obj = array();
    foreach($elements as $index => $element){
        $obj[$index] = array( "tag" => $element->tagName );
        foreach ($element->attributes as $attribute) {
            $obj[$index][$attribute->name] = $attribute->value;
        }
        // foreach ($element->childNodes as $subElement) {
        //     if ($subElement->nodeType == XML_TEXT_NODE) {
        //         $obj[$index]["html"] = $subElement->wholeText;
        //     }
        //     else {
        //         $obj[$index]["children"][] = element_to_obj($subElement);
        //     }
        // }
    }

    return $obj;
}


public function createUserToken(Request $request){
  if (empty($request->client_id)){
    return response() ->json(['message' => 'No device  found for specified account ', 'status' => 'error']);
  }
  
  $user = auth::user();
  $payload_data = $request->all();
  $datas['org'] = $user;
  $datas['api_url'] = 'api/authentication';
  $datas['payload'] = $payload_data;
  $this->trackApiDetail($datas);
  
  $isClientexist = $this->IsCLientExist($request->client_id);
  if($isClientexist!=1){
    return response() ->json(['message' => 'Device is not registered', 'status' => 'error']);
    
  }
  $url = getenv('MIDserver_PUBLIC_URL');
  $client_id = $request->client_id;
  $password  = $request->client_id;
  $url = getenv('MIDserver_PUBLIC_URL').'/oauth2/token';
  $data = ["grant_type"=>"client_credentials"];
  $headers = array(
    'Content-Type: application/json',
    'Authorization: Basic '. base64_encode("$client_id:$password")
   );
        $ch = curl_init($url); 
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
      //  print_r ($result);die;
      if (!empty($result)) {
        $client = Registration::where('device_id',$client_id)->first();
        $client->token = json_decode($result)->access_token;
        $client->auth_status = 1;
        $client->save();
        $instro = $this->instrospectToken(json_decode($result)->access_token);
        $notification_id = $client->fcm_device_id;
         // $notification_id = 'fn5IX8XwRBundf_ZxLXOrO:APA91bGjw_Ok3b7JyVFKUO1vUEQl2kzM7JW74kxnjV9na5mCrSidugus1BXGCw8WGvZyM1--FHU6sIgU71ikNBP75X3z8ZhUB_EbHKfNDGHPh6y5_GZpcDeJNrxfVXGJisH-nWkeNHEX';
            $message = ($request->pushPayload)?$request->pushPayload:'';
            $screen = 'authenticate';
            $fcm = $this->send_notification_FCM($notification_id,'Authentication',$message,$screen);
            $fcm = json_decode($fcm,true);
            // dd($fcm);
             if($fcm["success"]==1){
                // $trans =  ApiDetail::where(['client_id'=>$request->client_id,'api_url'=>'api/authentication'])->latest()->first();
                // $trans->status =1;
                // $trans->save();
             return response()
                ->json(['message' => 'Authorization token ', 'status' => true, 'data' => json_decode($result) ]);   
            }
            return response()
                ->json(['message' => 'Device token is not valid,Authentication Failed! ', 'status' => false, 'data' => [] ]);

      }
       return response()->json(['message'=>'Account donot exist','status'=>'error']);

}

   public function instrospectToken($token){
          $data = ["token"=>$token];
          $url = getenv('MIDserver_Admin_URL').'/oauth2/introspect';
          $ch = curl_init($url); 
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
         // curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $password);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
          $result = curl_exec($ch);
          $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);
          return $result;
    }

    public function consentSign(Request $request)
    {
        //print_r($request->all());
        if (empty($request->client_id))
        {
            return response()
                ->json(['message' => 'No Client  found for specified account ', 'status' => 'error']);
        }
        if (empty($request->device_id))
        {
            return response()
                ->json(['message' => 'No device  found for specified account ', 'status' => 'error']);
        }
        if (empty($request->preContextTitle))
        {
            return response()
                ->json(['message' => 'content text cannot not be empty', 'status' => 'error']);
        }
        $isClientexist = $this->IsCLientExist($request->client_id);
        if($isClientexist!=1){
          return response() ->json(['message' => 'Device is not registered', 'status' => 'error']);
          
        }
        $user = auth::user();
        $datas['org'] = $user;
        $payload_data = $request->all();
        $datas['api_url'] = 'api/consentSign';
        $datas['payload'] = $payload_data;
        $this->trackApiDetail($datas);

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $pushPayload  = $request->pushPayload?$request->pushPayload:'';
        $preContextContent  = $request->preContextContent?$request->preContextContent:'';
        // Create token payload as a JSON string
        $payload = json_encode(['client_id' => $request->client_id, 'device_id' => $request->device_id, 'push_payload' => $pushPayload,'preContextTitle' =>base64_decode($request->preContextTitle),'preContextContent'=>$preContextContent]);
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
        // return response()->json(['data' => $jwt, 'status' => 'success']);
        $notification_id = $this->getdeviceToken($request->client_id);
        $message = ($request->preContextTitle)?$request->preContextTitle:'';
        $message = base64_decode($message);
        $screen  = 'consentSign';
        $fcm = $this->send_notification_FCM($notification_id,'Consent Signature',$jwt,$screen);
       // return $jwt;
        $fcm = json_decode($fcm,true);
        if($fcm["success"]==1){
            return response()
            ->json(['message' => 'Consent Sign ', 'status' => 'success', 'data' => $jwt ]);   
        }
            return response()
            ->json(['message' => 'Consent Sign', 'status' => 'error', 'data' => [] ]);
        
        // return $jwt;
        
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
        if (empty($request->client_id))
        {
            return response()
                ->json(['message' => 'No Client  found for specified account ', 'status' => 'error']);
        }
        if (empty($request->device_id))
        {
            return response()
                ->json(['message' => 'No device  found for specified account ', 'status' => 'error']);
        }
        if (empty($request->preContextTitle))
        {
            return response()
                ->json(['message' => 'Payment information cannot not be empty', 'status' => 'error']);
        }
        $user = auth::user();
        $datas['org'] = $user;
        $payload_data = $request->all();
        $datas['api_url'] = 'api/authorizepayment';
        $datas['payload'] = $payload_data;
        $this->trackApiDetail($datas);

        $isClientexist = $this->IsCLientExist($request->client_id);
        if($isClientexist!=1){
          return response() ->json(['message' => 'Device is not registered', 'status' => 'error']);
          
        }
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
        // return true;
        $notification_id = $this->getdeviceToken($request->client_id);
        $message = ($request->preContextTitle)?$request->preContextTitle:'';
        $message = base64_decode($message);
        $screen  = 'PaymentAuthorization';
        $fcm = $this->send_notification_FCM($notification_id,'Payment Authorization',$message,$screen);
        $fcm = json_decode($fcm,true);
        if($fcm["success"]==1){
             return response()
                ->json(['message' => 'Authorization Payment Success', 'status' => 'success', 'data' => json_decode($jwt) ]);   
        }
            return response()
                ->json(['message' => 'Authorization Payment Failed ', 'status' => 'error', 'data' => [] ]);
       // return $jwt;
    }


    function trackApiDetail($data){
        $api = new ApiDetail;
        $api->org_id = $data['org']->id?$data['org']->id:'' ;
        $api->api_url = $data['api_url']?$data['api_url']:'' ;
        $api->client_id = $data['payload']?$data['payload']['client_id']:'' ;
        $api->payload = json_encode($data['payload']) ;
        $api->payload = json_encode($data['payload']) ;
        $api->status = 2 ;
        if($api->save()){
            return true;
        }; 
    }

    function getdeviceToken($client_id){
        $client = Registration::where('device_id', $client_id)->first();
        return  $client->fcm_device_id;
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
            // dd($datas);
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
              "message" : "'.$message.'",
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
        // if ($rest === false) {
        //     // throw new Exception('Curl error: ' . curl_error($crl));
        //     //print_r('Curl error: ' . curl_error($crl));
        //     $result_noti = 0;
        // } else {
        //     $result_noti = 1;
        // }
        // //curl_close($crl);
        // //print_r($result_noti);die;
        // return $result_noti;
}

}