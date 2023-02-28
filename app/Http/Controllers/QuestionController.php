<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Project;
use App\Models\Question;
use App\Models\Section;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request,$id)
    {
        Controller::validatePermissions($request->user(),'GET','/projects/{project}/surveys');
        $data=Survey::findOrFail($id);
        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('project_user.project_cod_project','=',$data->cod_project)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()){
            $idsection=isset($request->section)?$request->section:1;
            $section=$data->sections->where('cod_section', '=', $idsection);
            unset($data['sections']);
            $data['section']=$section;
            foreach ($data['section'] as $sec){
                $sec['questions']=Question::where('cod_section','=',$sec->cod_section)
                    ->orderby('order')
                    ->get();
                foreach ($sec['questions'] as $que){
                    $que['options']=Option::where('cod_question','=',$que->cod_question)->get();
                }
            }
            return $this->response('false', Response::HTTP_OK, '200 OK', $data);
        }

        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
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
        $data=Section::join('surveys','surveys.cod_survey','sections.cod_survey')
            ->where('cod_section','=',$id)
            ->firstOrFail();
        if($data->status==true){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }

        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('project_user.project_cod_project','=',$data->cod_project)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()){
            $data=[];
            $edit_permission=[
                'name',
                'question',
                'required',
                'type',
                'image',
                'order'
            ];
            foreach ($edit_permission as $d){
                if(isset($request->$d)){
                    $data[$d]=$request->$d;
                }
            }
            $data['cod_section']=$id;

            $validate=\Validator::make($data,[
                'type'=>'required|in:short_answer,long_text,multiple_choice,checkboxes,dropdown,date,time,datetime,numerical',
                'required'=>'boolean',
                'order'=>'required',
                'cod_section'=>'required|exists:sections,cod_section'
            ]);
            if ($validate->fails())
            {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }

            $question=Question::create($data);
            $log="The user '".$request->user()->id."' create new question a section $id";
            $this->log('info',$log,'web',$request->user());
            return $this->response(false, Response::HTTP_CREATED, '201 Created',$question);
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
    public function edit(Request $request,$id)
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
        $question=Question::join('sections','sections.cod_section','questions.cod_section')
                        ->join('surveys','surveys.cod_survey','sections.cod_survey')
                        ->where('cod_question','=',$id)
                        ->firstOrFail();

        if($question->status==true){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }

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
            $data=[];

            $edit_permission=[
                'name',
                'question',
                'required',
                'type',
                'image',
                'order'
            ];
            foreach ($edit_permission as $d){
                if(isset($request->$d)){
                    $data[$d]=$request->$d;
                }
            }

            $validate=\Validator::make($data,[
                'type'=>'in:short_answer,long_text,multiple_choice,checkboxes,dropdown,date,time,datetime,numerical,image',
                'required'=>'boolean',
            ]);
            if ($validate->fails())
            {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }
            $question->update($data);
            $log="The user '".$request->user()->id."' update a question $id";
            $this->log('info',$log,'web',$request->user());
            return $this->response(false, Response::HTTP_OK, '200 200 OK',$question);
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
        $question=Question::join('sections','sections.cod_section','questions.cod_section')
            ->join('surveys','surveys.cod_survey','sections.cod_survey')
            ->where('cod_question','=',$id)
            ->firstOrFail();

        if($question->status==true){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }

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
            $question->delete();
            return $this->response('false', Response::HTTP_OK, '200 OK');
        }
        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
    }
}
