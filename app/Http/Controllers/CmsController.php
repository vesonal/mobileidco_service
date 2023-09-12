<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pages;

class CmsController extends Controller
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

    public function privacyPolicy(Request $request){
        $privacy = Pages::where('pagename','privacy_policy')->first();
        return view('privacy_policy',compact('privacy'));
    }


    public function aboutUs(Request $request){
        $about = Pages::where('pagename','about_us')->first();
        return view('about_us',compact('about'));
    }

    public function termsCondition(Request $request){
        $terms = Pages::where('pagename','terms_condition')->first();
        return view('terms_condition',compact('terms'));
    }

}
