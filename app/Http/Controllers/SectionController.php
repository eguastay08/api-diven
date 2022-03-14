<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Section;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SectionController extends Controller
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
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $id)
    {
        Controller::validatePermissions($request->user(),'POST','/projects/{project}/surveys');
        $survey=Survey::findOrFail($id);
        if($survey->status==true){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }
        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('project_user.project_cod_project','=',$survey->cod_project)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()){
            $data=[];
            $edit_permission=[
                'name',
                'detail',
                'order',
                'cod_survey'
            ];

            foreach ($edit_permission as $d){
                if(isset($request->$d)){
                    $data[$d]=$request->$d;
                }
            }
            $data['cod_survey']=$survey->cod_survey;
            $validate=\Validator::make($data,[
                'name'=>'required',
                'order'=>'integer|required',
                'cod_survey'=>'required|exists:surveys,cod_survey'
            ]);
            if ($validate->fails())
            {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }

            $survey=Section::create($data);
            $log="The user '".$request->user()->id."' create new section a survey $id";
            $this->log('info',$log,'web',$request->user());
            return $this->response(false, Response::HTTP_CREATED, '201 Created',$survey);
        }
        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        Controller::validatePermissions($request->user(),'POST','/projects/{project}/surveys');
        $section=Section::join('surveys','surveys.cod_survey','sections.cod_survey')
                        ->where('cod_section','=',$id)
                        ->firstOrFail();
        if($section->status==true){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }
        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->join('surveys','surveys.cod_project','projects.cod_project')
                ->join('sections','sections.cod_survey','surveys.cod_survey')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('sections.cod_section','=',$section->cod_section)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()){
            $data=[];
            $edit_permission=[
                'name',
                'detail',
                'order'
            ];

            foreach ($edit_permission as $d){
                if(isset($request->$d)){
                    $data[$d]=$request->$d;
                }
            }
            $validate=\Validator::make($data,[
                'order'=>'integer'
            ]);
            if ($validate->fails())
            {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }
            $section->update($data);
            $log="The user '".$request->user()->id."' update a section $id";
            $this->log('info',$log,'web',$request->user());
            return $this->response(false, Response::HTTP_OK, '200 200 OK',$section);
        }

        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        Controller::validatePermissions($request->user(),'POST','/projects/{project}/surveys');
        $section=Section::join('surveys','surveys.cod_survey','sections.cod_survey')
            ->where('cod_section','=',$id)
            ->firstOrFail();
        if($section->status==true){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }
        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->join('surveys','surveys.cod_project','projects.cod_project')
                ->join('sections','sections.cod_survey','surveys.cod_survey')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('sections.cod_section','=',$section->cod_section)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()){
            $section->delete();
            return $this->response('false', Response::HTTP_OK, '200 OK');
        }
        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
    }
}
