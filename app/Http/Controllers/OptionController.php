<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Project;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OptionController extends Controller
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
    public function store(Request $request,$id)
    {
        Controller::validatePermissions($request->user(),'POST','/projects/{project}/surveys');
        $question=Question::findOrFail($id);
        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->join('surveys','surveys.cod_project','projects.cod_project')
                ->join('sections','sections.cod_survey','surveys.cod_survey')
                ->join('questions','questions.cod_section','sections.cod_section')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('questions.cod_section','=',$question->cod_section)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()){
            $edit_permission=[
                'option',
                'image',
            ];
            foreach ($edit_permission as $d){
                if(isset($request->$d)){
                    $data[$d]=$request->$d;
                }
            }
            $data['cod_question']=$id;

            $validate=\Validator::make($data,[
                'option'=>'required|unique:options,option,NULL,id,cod_question,'.$data['cod_question'],
                'cod_question'=>'required|exists:questions,cod_question'
            ]);
            if ($validate->fails())
            {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }
            $option=Option::create($data);
            $log="The user '".$request->user()->id."' create new option from question $id";
            $this->log('info',$log,'web',$request->user());
            return $this->response(false, Response::HTTP_CREATED, '201 Created',$option);
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
        $option=Option::findOrFail($id);
        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->join('surveys','surveys.cod_project','projects.cod_project')
                ->join('sections','sections.cod_survey','surveys.cod_survey')
                ->join('questions','questions.cod_section','sections.cod_section')
                ->join('options','options.cod_question','questions.cod_question')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('options.cod_question','=',$option->cod_question)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()){
            $edit_permission=[
                'option',
                'image',
            ];
            foreach ($edit_permission as $d){
                if(isset($request->$d)){
                    $data[$d]=$request->$d;
                }
            }

            $validate=\Validator::make($data,[
                'option'=>'unique:options,option,NULL,id,cod_question,'.$option->cod_question
            ]);
            if ($validate->fails())
            {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }

            $option->update($data);
            $log="The user '".$request->user()->id."' update a option $id";
            $this->log('info',$log,'web',$request->user());
            return $this->response(false, Response::HTTP_OK, '200 200 OK',$option);
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
    public function destroy(Request $request,$id)
    {
        Controller::validatePermissions($request->user(),'POST','/projects/{project}/surveys');
        $option=Option::findOrFail($id);
        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->join('surveys','surveys.cod_project','projects.cod_project')
                ->join('sections','sections.cod_survey','surveys.cod_survey')
                ->join('questions','questions.cod_section','sections.cod_section')
                ->join('options','options.cod_question','questions.cod_question')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('options.cod_question','=',$option->cod_question)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()){
                $option->delete();
            return $this->response('false', Response::HTTP_OK, '200 OK');
        }
        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
    }
}
