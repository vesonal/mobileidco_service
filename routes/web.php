<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('login', [CustomAuthController::class, 'index'])->name('login');

Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom'); 
Route::get('registration', [CustomAuthController::class, 'registration'])->name('register-user');
Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom'); 
Route::get('signout', [CustomAuthController::class, 'signOut'])->name('signout');

Route::group(['middleware' => 'auth'], function () {
	Route::get('dashboard', [CustomAuthController::class, 'dashboard'])->name('dashboard'); 
	Route::get('tokenList', function () {
    	return view('token_list');
	});
	Route::get('/configuration', 'MobileConfigurationController@getConfiguration')->name('configuration');
	Route::get('/mobile-registration', 'MobileConfigurationController@mobileRegistration')->name('mobile-registration');
	Route::get('/authentication', 'MobileConfigurationController@authentication')->name('authentication');
	Route::get('/authorization', 'MobileConfigurationController@authorization')->name('authorization');
	Route::get('/consent-sign', 'MobileConfigurationController@consentSign')->name('consent-sign');
	Route::post('/client', 'Api\RegisterController@create');
	Route::get('/client/{id}', 'Api\RegisterController@getAvailableDevice');
	Route::get('/api/jwt', 'Api\RegisterController@convertPayloasJwt');
	Route::post('/consentSign', 'Api\RegisterController@consentSign');
    Route::post('/create-token', 'Api\RegisterController@createUserToken');
    Route::post('/authorizepayment', 'Api\RegisterController@authorizePayment');
    
Route::get('/api/test',function(){
	return 'ok';
});

});

Route::post('/api/checkstatuss', 'Api\RegisterController@checkStatus');

Route::get('/app/privacy-statement','CmsController@privacyPolicy');
Route::get('/app/aboutUs','CmsController@aboutUs');
Route::get('/app/terms-condition','CmsController@termsCondition');

//Route::post('/api/otpverify', 'Api\RegisterController@verifyActivationCode');
