<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Project;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SurveyController extends Controller
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
        if(Project::select('projects.*')
            ->join('project_user','projects.cod_project','project_user.project_cod_project')
            ->where('project_user.user_id','=',$request->user()->id)
            ->where('project_user.project_cod_project','=',$id)
            ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()){
            $data=Project::findOrFail($id)->surveys;
            return $this->response('false', Response::HTTP_OK, '200 OK', $data);
        }

        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
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
        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('project_user.project_cod_project','=',$id)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()){

            $data=[];
            $edit_permission=[
                'name',
                'date_init',
                'date_finally',
                'max_answers',
                'detail'
            ];

            foreach ($edit_permission as $d){
                if(isset($request->$d)){
                    $data[$d]=$request->$d;
                }
            }
            $data['status']=false;
            $data['cod_project']=$id;
            $data['max_answers']=isset($data['max_answers'])?$data['max_answers']:-1;

            $validate=\Validator::make($data,[
                'name'=>'required|unique:surveys,name,NULL,id,cod_project,'.$data['cod_project'],
                'date_init' => 'required|date_format:Y-m-d\TH:i:s.\0\0\0\Z|after:today',
                'date_finally' => 'date_format:Y-m-d\TH:i:s.\0\0\0\Z|after:date_init',
                'status'    => 'required|boolean',
                'max_answers'=>'gte:-1',
                'cod_project'=>'required|exists:projects,cod_project'
            ]);

            if ($validate->fails())
            {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }

            $survey=Survey::create($data);
            $log="The user '".$request->user()->id."' create new survey a project $id";
            $this->log('info',$log,'web',$request->user());
            return $this->response(false, Response::HTTP_CREATED, '201 Created',$survey);
        }

        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request$request, $id)
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
            $data['sections']=$data->sections;
            foreach ($data['sections'] as $sec){
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        Controller::validatePermissions($request->user(),'PUT','/surveys');
        $survey=Survey::findOrFail($id);
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
                'date_finally',
                'max_answers',
                'status',
                'detail'
            ];

            foreach ($edit_permission as $d){
                if(isset($request->$d)){
                    $data[$d]=$request->$d;
                }
            }
            $validate=\Validator::make($data,[
                'date_finally' => 'date_format:Y-m-d\TH:i:s.\0\0\0\Z',
                'status'    => 'boolean',
                'max_answers'=>'gte:-1'
            ]);

            if ($validate->fails())
            {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }
            if($survey->status!=0){
                unset($data['status']);
            }
            $survey->update($data);
            $log="The user '".$request->user()->id."' update a survey $id";
            $this->log('info',$log,'web',$request->user());
            return $this->response(false, Response::HTTP_OK, '200 200 OK',$survey);
        }
        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
    }

    /**
     *  Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request,$id)
    {
        Controller::validatePermissions($request->user(),'DELETE','/surveys');
        $survey=Survey::findOrFail($id);
        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('project_user.project_cod_project','=',$survey->cod_project)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()){
            $survey->delete();
            return $this->response('false', Response::HTTP_OK, '200 OK');
        }
        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
    }
}
