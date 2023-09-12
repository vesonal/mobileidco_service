<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiSetting;
use Redirect;
use Session;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = ApiSetting::get();
        return view('admin.settingList',compact('setting'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.settingAdd');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $setting  = new ApiSetting;
        $setting->api_end_url = $request->api_end_url?$request->api_end_url:'';
        $setting->api_key = $request->api_key?$request->api_key:'';
        $setting->fcm_key = $request->fcm_key?$request->fcm_key:'';

        // if($setting->save()){
        //     Session::flash('message', 'Created successfully');
        //    Session::flash('alert-class', 'alert-success');
        //    return Redirect::back()->with('message', 'Created successfully'); 
        // }
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
        $setting = ApiSetting::find($id);
        return view('admin.settingEdit',compact('setting'));
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
        $setting = ApiSetting::find($id);
        $setting->api_end_url = $request->api_end_url?$request->api_end_url:'';
        $setting->api_key = $request->api_key?$request->api_key:'';
        $setting->fcm_key = $request->fcm_key?$request->fcm_key:'';
        if($setting->save()){
           Session::flash('message', 'Updated successfully');
           Session::flash('alert-class', 'alert-success');
           return Redirect::back()->with('message', 'updated successfully'); 
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $setting = ApiSetting::find($id);
        if($setting->delete()){
            Session::flash('message', 'Deleted successfully');
            Session::flash('alert-class', 'alert-success');
                return redirect()->route('setting.list');
        }
    }
}
