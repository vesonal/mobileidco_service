<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Session;
use Redirect;

class AdminController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
    	//return'welcome on dashboard';
        if(Auth::guard('admin')->check()){
            $user = auth()->guard('admin')->user();
            //dd($user);
            if ($user) {
            return view('admin_dashboard'); 
            }
        }
        return view('auth.admin.login');
    }

    public function index(){
        $userLists = Admin::paginate(10);
        return view('admin.userList',compact('userLists'));
    }

    public function destroy($id){
       if ($id!==1) {
            $res = Admin::find($id)->delete();
        if ($res) {
            Session::flash('message', 'Deleted successfully');
            Session::flash('alert-class', 'alert-success');
         return redirect()->route('userList');
         }   
         }   
    }
     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = Admin::find($id);
        
        return view('admin.userDetail',compact('result'));

    }

    public function edit($id){
        $adminData = Admin::find($id);
        return view('admin.userEdit',compact('adminData'));
    }

    public function create(){
        return view('admin.userAdd');
    }

    public function store(Request $request){

        $validatedData = $request->validate([
        'name' => ['required'],
        'email' => ['required', 'unique:admins'],
        'password' => ['required','confirmed','min:6'],
        ]);
        $inputs=$request->all();
        $inputs['password']=Hash::make($inputs['password']);
        $admin = Admin::create($inputs);
        if ($admin) {
           Session::flash('message', 'Created successfully');
           Session::flash('alert-class', 'alert-success');
           return redirect()->route('userList')->with('message', 'Created successfully'); 
        }
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
        $admin = Admin::find($id);
        $admin->name = $request->name;
        if($admin->save()){
            Session::flash('message', 'Updated successfully');
            Session::flash('alert-class', 'alert-success');
           return redirect()->route('userList')->with('message', 'Updated successfully'); 
        }

    }

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getAllUsers(Request $request){
        $columns        = array('id','name','email','id');
        $totalData      = Admin::count();
        $totalFiltered  = $totalData;
        $limit          = $request->input('length');
        $start          = $request->input('start');
        $order          = $columns[$request->input('order.0.column')];
        $dir            = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $userLists     = Admin::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value');
            $userLists =  Admin::where(function($query) use ($search) {
                                $query->where('name','LIKE',"%$search%")
                                    ->orWhere('email', 'LIKE',"%$search%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = Admin::where(function($query) use ($search) {
                                        $query->where('name','LIKE',"%$search%")
                                            ->orWhere('email', 'LIKE',"%$search%");
                                    })
                                ->count();
        }

        $data = array();
        if(!empty($userLists))
        {
            if($dir=='desc'){
              $i=($start==0)?1:($start+1);
            }else{
              $i=$totalData-$start;
            }
            foreach ($userLists as $adminuser)
            {
                $nestedData['id']        = $i;
                $nestedData['name']      = $adminuser->name;
                $nestedData['email']     = $adminuser->email;

                $deleteMsg  = '"Are you sure you wish to delete?"';
                $editUrl    = "<a href='".route('userEdit', [$adminuser->id])."' class='btn btn-xs btn-primary pull-center'>Edit</a>";
                $deleteUrl  = "<a href='".route('userDelete', [$adminuser->id])."' class='btn btn-xs btn-primary pull-center' onclick='return confirm(".$deleteMsg.")' >Delete</a>";

                $nestedData['action']   = $editUrl.' '.$deleteUrl;
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