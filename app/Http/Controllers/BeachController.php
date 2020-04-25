<?php

namespace App\Http\Controllers;

use App\Beach;
use Illuminate\Http\Request;

class BeachController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Beach::all();
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
     * @param  \App\Beach  $beach
     * @return \Illuminate\Http\Response
     */
    public function show(Beach $beach)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Beach  $beach
     * @return \Illuminate\Http\Response
     */
    public function edit(Beach $beach)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Beach  $beach
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Beach $beach)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Beach  $beach
     * @return \Illuminate\Http\Response
     */
    public function destroy(Beach $beach)
    {
        //
    }
}
