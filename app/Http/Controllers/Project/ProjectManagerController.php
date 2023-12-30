<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\ProjectManager;
use Illuminate\Http\Request;

class ProjectManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        
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
     * @param  \App\Models\ProjectManager  $projectManager
     * @return \Illuminate\Http\Response
     */
    public function show(ProjectManager $projectManager)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProjectManager  $projectManager
     * @return \Illuminate\Http\Response
     */
    public function edit(ProjectManager $projectManager)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProjectManager  $projectManager
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProjectManager $projectManager)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProjectManager  $projectManager
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectManager $projectManager)
    {
        //
    }
}
