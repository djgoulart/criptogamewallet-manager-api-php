<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Project;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class ProjectsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::all();
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|unique:projects|max:255',
            'url' => 'required|url',
            'network' => ['required', Rule::in(['bsc', 'eth', 'polygon'])],
            'is_active' => 'nullable|boolean'
        ];

        $this->validate($request, $validationRules);
        $project = Project::create($request->all());
        $project->refresh();

        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $project = Project::findOrFail($id);
            return response()->json($project);
        } catch (ModelNotFoundException $e) {
            return response()->json("Not found", 404);
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
        $validationRules = [
            'name' => [
                'max:255',
                Rule::unique('projects')->ignore($id)
            ],
            'url' => 'url',
            'network' => [Rule::in(['bsc', 'eth', 'polygon'])],
            'is_active' => 'boolean'
        ];

        $this->validate($request, $validationRules);
        $project = Project::find($id);

        $project->update($request->all());
        $project->refresh();
        return response()->json($project);
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
