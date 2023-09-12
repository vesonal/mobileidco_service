<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test-auth',function(){
	 $state = str_shuffle('sdjfgshdfgshdvfbvbsdfhfds');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:4444/oauth2/auth/?audience=&client_id='sampleuser'&max_age=0&prompt=&redirect_uri=http%3A%2F%2F127.0.0.1%3A5555%2Fcallback&response_type=code+id_token&scope=openid+offline&state=".$state."");
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	print_r($response);
	 // $file = file_get_contents('http://127.0.0.1:4445/oauth2/auth?audience=&client_id=sampletest&max_age=0&prompt=&redirect_uri=http%3A%2F%2F127.0.0.1%3A5555%2Fcallback&response_type=code&scope=openid+offline&state=".$state."',true);
	 // if($file){
	 // 	print_r($file);
	 // 	//print_r(csrf_token());
	 // }

});


Route::post('/oauth2/auth','Api\RegisterController@createOauth');
//to get client by ory hydra server

Route::get('/getclient',function(Request $request){
$client = new Client();
    $response = $client->get("http://127.0.0.1:4445/clients");
    $response = (string) $response->getBody();
    
    return response()->json(['data' => json_decode($response),'message'=>'client list'], 200);
});

Route::post('create-tokenss', 'Api\RegisterController@createUserToken');

Route::post('get-client',function(Request $request){
	
	$url = 'http://127.0.0.1:4445/clients';

	$data = '{
	    "client_id": "sampleuser",
	    "client_name": "sampleuser",
	    "client_secret": "sampleuser",
	    "redirect_uris": [],
	    "grant_types": [
	        "client_credentials"
	    ],
	    "response_types": [
	        "code"
	    ],
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

	$postdata = $data;
	$ch = curl_init($url); 
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	//print_r ($httpcode);
	if ($httpcode==201) {
		$res = json_decode($result,true);
		$client_id = $res['client_id'];
		$client_name = $res['client_name'];
		$code = mt_rand(100000,999999);
		$values = array('device_id' => $client_id,'device_name'=>$client_name,'activation_code'=>$code,'status'=>0);
		$query = DB::table('registration')->insert($values);
		if ($query) {
			return response()->json(['data' => $code,'message'=>'activation code','status'=>200]);
		}
	}
	else{
		return response()->json(['message'=>'Unable to insert or update resource because a resource with that value exists already','status'=>500], 500);
	}

});

Route::get('/testfg',function(){
	return 'ok';
});

//without authentication 
Route::post('/VerifyBind', 'Api\ApiController@verifyActivationCode');
Route::get('/aboutUs', 'Api\ApiController@aboutUs');
Route::get('/getConfigurations', 'Api\ApiController@getConfigurations');
Route::post('/VerifyBind', 'Api\ApiController@verifyActivationCode');

//for mobileid
Route::post('/login','Api\ApiController@login');
Route::group(['middleware' => 'auth:api'], function () {
	
	Route::post('/registration', 'Api\ApiController@create');
	//For fetch client 
	Route::get('/client/{id}', 'Api\ApiController@getAvailableDevice');
	//for consent sign
	Route::post('/consent-sign', 'Api\ApiController@consentSign');
	Route::post('/authentication', 'Api\ApiController@createUserToken');
	Route::post('/payment-authorization', 'Api\ApiController@authorizePayment');

});
//check status 
Route::post('register/checkStatus','Api\ApiController@RegisterCheckStatus');
Route::post('authenticate/checkStatus','Api\ApiController@AuthenticateCheckStatus');
Route::post('authorizepayment/checkStatus','Api\ApiController@PaymentCheckStatus');
Route::post('consentsign/checkStatus','Api\ApiController@ConsentCheckStatus');
//update status

Route::post('authenticate/updateStatus', 'Api\ApiController@AuthenticationUpdateStatus');
Route::post('authorizepayment/updateStatus', 'Api\ApiController@PaymentUpdateStatus');
Route::post('consentsign/updateStatus', 'Api\ApiController@ConsentUpdateStatus');

//deactivate
Route::post('deactivateDevice','Api\ApiController@deactivateDevice');

//Device pin
Route::post('register/pin','Api\ApiController@SaveregisterPin');
Route::post('register/getdevicepin','Api\ApiController@getdevicePin');
Route::post('register/update/pin','Api\ApiController@updateRegisterPin');

//history
Route::post('client/history','Api\ApiController@historyDetail');