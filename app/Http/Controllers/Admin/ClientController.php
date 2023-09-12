<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Registration;
use Session;
use Redirect;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	//$clientList = Registration::paginate(10);
    	$clientList = Registration::where('status',1)->paginate(10);

        return view('admin.clientList',compact('clientList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

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
        $url = getenv('MIDserver_ADMIN_URL') . '/clients/'.$id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
        $result = curl_exec($ch);
        $result = json_decode($result);
        curl_close($ch);
       // print_r($result);
        return view('admin.clientDetail',compact('result'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $url = getenv('MIDserver_ADMIN_URL') . '/clients/'.$id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
        $result = curl_exec($ch);
        $result = json_decode($result);
        curl_close($ch);
        return view('admin.clientEdit',compact('result'));
        
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
        $client_id = $request['client_id'];
        $client_name = $request['device_name'];
        $url = getenv('MIDserver_ADMIN_URL') . '/clients/'.$request->client_id;
        //  "grant_types": ["authorization_code","refresh_token"],
        $data = '{
            "client_id": "' . $client_id. '",
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

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $res = json_decode($result, true);
        if (!empty($res)) {
            $res = Registration::where('device_id',$client_id)->first();
            $res->device_name = $client_name;
            $res->save();
            Session::flash('message', 'Updated successfully');
            Session::flash('alert-class', 'alert-success');
                 return redirect()->route('clientList')->with('message', 'Updated successfully');
        }
        //print_r($res);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $url = getenv('MIDserver_ADMIN_URL') . '/clients/'.$id;
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  
	    $result = curl_exec($ch);
	    $result = json_decode($result);
	    curl_close($ch);
	   // print_r($result);
	  if (empty($result)) {
	  	$res = Registration::where('device_id',$id)->delete();
	  	Session::flash('message', 'Deleted successfully');
        Session::flash('alert-class', 'alert-success');
            return redirect()->route('clientList');

	  }
	   // print_r($result);
	}

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getAllClient(Request $request){
        $columns        = array('id','title','status','created_at','id');
        $totalData      = Registration::where('status',1)->count();
        $totalFiltered  = $totalData;
        $limit          = $request->input('length');
        $start          = $request->input('start');
        $order          = $columns[$request->input('order.0.column')];
        $dir            = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $clients     = Registration::where('status',1)->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value');
            $clients =  Registration::where(['status'=>1])
                            ->where(function($query) use ($search) {
                                $query->where('device_id','LIKE',"%$search%")
                                    ->orWhere('device_name', 'LIKE',"%$search%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = Registration::where(['status'=>1])
                                    ->where(function($query) use ($search) {
                                        $query->where('device_id','LIKE',"%$search%")
                                            ->orWhere('device_name', 'LIKE',"%$search%");
                                    })
                                ->count();
        }

        $data = array();
        if(!empty($clients))
        {
            if($dir=='desc'){
              $i=($start==0)?1:($start+1);
            }else{
              $i=$totalData-$start;
            }
            foreach ($clients as $client)
            {
                $nestedData['id']          	    = $i;
                $nestedData['device_id']       	= $client->device_id;
                $nestedData['device_name']      = $client->device_name;

                $viewUrl    = "<a href='".route('clientDetail', [$client->device_id])."' class='btn btn-xs btn-primary pull-center'>View</a>";
                $deleteMsg  = '"Are you sure you wish to delete?"';
                $editUrl    = "<a href='".route('clientEdit', [$client->device_id])."' class='btn btn-xs btn-primary pull-center'>Edit</a>";
                $deleteUrl  = "<a href='".route('clientDelete', [$client->device_id])."' class='btn btn-xs btn-primary pull-center' onclick='return confirm(".$deleteMsg.")' >Delete</a>";

                $nestedData['action']   = $viewUrl.' '.$editUrl.' '.$deleteUrl;
                $data[] = $nestedData;
                if($dir=='desc'){ $i++; }else{ $i--; }
            }
        }
        $json_data = array(
                    "draw"            => intval($request->input('draw')),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $data
                    );

        echo json_encode($json_data);
    }
}
