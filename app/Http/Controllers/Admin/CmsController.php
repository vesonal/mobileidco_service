<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Pages;
use Session;
use Redirect;

class CmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = Pages::get();
        return view('admin.cmsList',compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.addcms');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $page = new Pages;
        $page->pagename = $request->pagename;
        $page->title = $request->title;
        $page->short_description = $request->short_description;
        $page->description = $request->description;
        if ($page->save()) {
           Session::flash('message', 'Created successfully');
           Session::flash('alert-class', 'alert-success');
           return redirect()->route('cms.list')->with('message', 'Created successfully'); 
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
        $pages = Pages::find($id);
        return view('admin.cmsEdit',compact('pages'));
    }

    
    public function update(Request $request, $id)
    {
        $pages = Pages::find($id);
        $pages->title = $request->title;
        $pages->keyword = $request->keyword;
        $pages->short_description = $request->short_description;
        $pages->description = $request->description;
        if ($pages->save()) {
           Session::flash('message', 'Updated successfully');
           Session::flash('alert-class', 'alert-success');
           return redirect()->route('cms.list')->with('message', 'Updated successfully'); 
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
        //
    }
}
