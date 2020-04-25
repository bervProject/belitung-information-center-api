<?php

namespace App\Http\Controllers\API;

use App\Beach;
use App\Http\Controllers\Controller;
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,
        [
            'name' => ['required'],
            'address' => ['required'],
            'description' => ['required']
        ]);
        return Beach::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Beach  $beach
     * @return \Illuminate\Http\Response
     */
    public function show(Beach $beach)
    {
        return Beach::findOrFail($beach->id);
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
        $foundBeach = Beach::findOrFail($beach->id);
        $this->validate($request,
        [
            'name' => ['required'],
            'address' => ['required'],
            'description' => ['required']
        ]);
        $foundBeach->update($request->all());
        return $foundBeach;
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
