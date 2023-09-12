<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::get('test',function(){
	return'ok';
});
Route::get('login','Auth\AdminAuthController@getLogin')->name('adminLogin');

//Route::get('admin/login','Auth\AdminAuthController@getLogin')->name('adminLogin');
Route::post('login', 'Auth\AdminAuthController@postLogin')->name('adminLoginPost');
Route::get('logout', 'Auth\AdminAuthController@logout')->name('adminLogout');

Route::group(['middleware' => 'adminauth'], function () {

	// Admin Dashboard
	// Route::get('dashboard',function(){
	// 	return  auth()->guard('admin')->user()->name ;
	// })->name('dashboard');	

	Route::get('dashboard','Admin\AdminController@dashboard')->name('dashboard');
	Route::get('client/list','Admin\ClientController@index')->name('clientList');
	Route::get('client/delete/{id}','Admin\ClientController@destroy')->name('clientDelete');
	Route::get('client/detail/{id}','Admin\ClientController@show')->name('clientDetail');
	Route::get('client/edit/{id}','Admin\ClientController@edit')->name('clientEdit');
	Route::post('client/update/{id}','Admin\ClientController@update')->name('clientUpdate');
	Route::post('client/getAllClient','Admin\ClientController@getAllClient')->name('clients.getAllClient');

	Route::group(['prefix'=>'user'],function(){
		Route::get('list','Admin\AdminController@index')->name('userList');
		Route::get('delete/{id}','Admin\AdminController@destroy')->name('userDelete');
		Route::get('detail/{id}','Admin\AdminController@show')->name('userDetail');
		Route::get('edit/{id}','Admin\AdminController@edit')->name('userEdit');
		Route::post('update/{id}','Admin\AdminController@update')->name('userUpdate');
		Route::get('add','Admin\AdminController@create')->name('usercreate');
		Route::post('add','Admin\AdminController@store')->name('userStore');
		Route::post('getAllUsers','Admin\AdminController@getAllUsers')->name('users.getAllUsers');
	});

	Route::group(['prefix'=>'organization'],function(){
		Route::get('list','Admin\OrganizationController@index')->name('org.list');
		Route::get('delete/{id}','Admin\OrganizationController@destroy')->name('org.delete');
		Route::get('detail/{id}','Admin\AdminController@show')->name('userDetail');
		Route::get('edit/{id}','Admin\OrganizationController@edit')->name('org.edit');
		Route::post('update/{id}','Admin\OrganizationController@update')->name('org.update');
		Route::get('show/{id}','Admin\OrganizationController@show')->name('org.show');
		Route::get('add','Admin\OrganizationController@create')->name('org.create');
		Route::post('add','Admin\OrganizationController@store')->name('org.store');
		Route::post('getAllOrganization','Admin\OrganizationController@getAllOrganization')->name('org.getAllOrganization');
	});

	Route::group(['prefix'=>'billing'],function(){ 
		// Route::get('list','Admin\OrganizationController@index')->name('org.list');
		// Route::get('delete/{id}','Admin\OrganizationController@destroy')->name('org.delete');
		// Route::get('detail/{id}','Admin\AdminController@show')->name('userDetail');
		// Route::get('edit/{id}','Admin\OrganizationController@edit')->name('org.edit');
		// Route::post('update/{id}','Admin\OrganizationController@update')->name('org.update');
		// Route::get('show/{id}','Admin\OrganizationController@show')->name('org.show');
		Route::get('add','Admin\BillingController@create')->name('billing.create');
		Route::post('add','Admin\BillingController@store')->name('billing.store');
	});

	Route::group(['prefix'=>'cms'],function(){ 
		Route::get('list','Admin\CmsController@index')->name('cms.list');
		// Route::get('delete/{id}','Admin\OrganizationController@destroy')->name('org.delete');
		Route::get('edit/{id}','Admin\CmsController@edit')->name('cms.edit');
		Route::post('update/{id}','Admin\CmsController@update')->name('cms.update');
		Route::get('add','Admin\CmsController@create')->name('cms.create');
		Route::post('add','Admin\CmsController@store')->name('cms.store');
	});

	Route::group(['prefix'=>'setting'],function(){ 
		Route::get('list','Admin\SettingController@index')->name('setting.list');
		Route::get('edit/{id}','Admin\SettingController@edit')->name('setting.edit');
		Route::post('update/{id}','Admin\SettingController@update')->name('setting.update');
		Route::get('add','Admin\SettingController@create')->name('setting.create');
		Route::post('add','Admin\SettingController@store')->name('setting.store');
		Route::get('delete/{id}','Admin\SettingController@destroy')->name('setting.delete');
	});


});





