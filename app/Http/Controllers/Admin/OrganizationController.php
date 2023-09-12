<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Organization;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Session;
use DB;
use Redirect;
use App\ApiDetail;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orgLists = Organization::paginate(10);
        return view('admin.organizationList',compact('orgLists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.organizationAdd');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $validatedData = $request->validate([
        'name' => ['required'],
        'email' => ['required', 'unique:organizations'],
        'password' => ['required','confirmed','min:6'],
        'contact_no' => ['required','numeric'],
        ]);
        $inputs=$request->all();
        $inputs['password']=Hash::make($inputs['password']);
        if($request->selected_option) { 
        $inputs['selected_option'] = implode(",",$request->selected_option); 
        }
        $org = Organization::create($inputs);
        if ($org){
           Session::flash('message', 'Created successfully');
           Session::flash('alert-class', 'alert-success');
           return redirect()->route('org.list')->with('message', 'Created successfully'); 
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
        $org = Organization::find($id);
        $apiDetailsArr = [];
        #90days date
        $startDate90      = date('Y-m-d', strtotime("-90 days"))." 00:00:00";
        $endDate           = date('Y-m-d')." 23:59:59";

        #30days date
        $startDate30      = date('Y-m-d', strtotime("-30 days"))." 00:00:00";

        //$apidetail = ApiDetail::selectRaw('count(*) as total, org_id,api_url')->where('org_id',$id)->groupBy('api_url')->get();

        $apidetails = ApiDetail::select(DB::raw('COUNT(id) as total'),'org_id','api_url')->where('org_id',$id)->groupBy('api_url')->get();
        foreach($apidetails as $singleApiDetails){

            $apiDetailsArr['api_url']           = $singleApiDetails->api_url;
            $apiDetailsArr['count30Days']       = ApiDetail::where(['org_id'=>$id,'api_url'=>$singleApiDetails->api_url])->whereBetween('created_at', [$startDate30, $endDate])->count();
            $apiDetailsArr['count90Days']       = ApiDetail::where(['org_id'=>$id,'api_url'=>$singleApiDetails->api_url])->whereBetween('created_at', [$startDate90, $endDate])->count();
            $apiDetailsArr['countTotalDays']    = $singleApiDetails->total;

            $finalApiDetails[] = $apiDetailsArr;

        }

        return view('admin.organizationDetail',compact('org','finalApiDetails'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $org = Organization::find($id);
        return view('admin.organizationEdit',compact('org'));
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
        $org = Organization::find($id);
        $org->name = $request->name;
        $org->country = $request->country;
        $org->contact_no = $request->contact_no;
        $org->selected_option = implode(",",$request->selected_option); 
        if($org->save()){
            Session::flash('message', 'Updated successfully');
            Session::flash('alert-class', 'alert-success');
           return redirect()->route('org.list')->with('message', 'Updated successfully'); 
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
        $res = Organization::find($id)->delete();
        Session::flash('message', 'Deleted successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect()->route('org.list');
    }

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getAllOrganization(Request $request){
        $columns        = array('id','name','email','id');
        $totalData      = Organization::count();
        $totalFiltered  = $totalData;
        $limit          = $request->input('length');
        $start          = $request->input('start');
        $order          = $columns[$request->input('order.0.column')];
        $dir            = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $organizations     = Organization::offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value');
            $organizations =  Organization::where(function($query) use ($search) {
                                $query->where('name','LIKE',"%$search%")
                                    ->orWhere('email', 'LIKE',"%$search%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = Organization::where(function($query) use ($search) {
                                        $query->where('name','LIKE',"%$search%")
                                            ->orWhere('email', 'LIKE',"%$search%");
                                    })
                                ->count();
        }

        $data = array();
        if(!empty($organizations))
        {
            if($dir=='desc'){
              $i=($start==0)?1:($start+1);
            }else{
              $i=$totalData-$start;
            }
            foreach ($organizations as $organization)
            {
                $nestedData['id']        = $i;
                $nestedData['name']      = $organization->name;
                $nestedData['email']     = $organization->email;

                $viewUrl    = "<a href='".route('org.show', [$organization->id])."' class='btn btn-xs btn-primary pull-center'>View</a>";
                $deleteMsg  = '"Are you sure you wish to delete?"';
                $editUrl    = "<a href='".route('org.edit', [$organization->id])."' class='btn btn-xs btn-primary pull-center'>Edit</a>";
                $deleteUrl  = "<a href='".route('org.delete', [$organization->id])."' class='btn btn-xs btn-primary pull-center' onclick='return confirm(".$deleteMsg.")' >Delete</a>";

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
