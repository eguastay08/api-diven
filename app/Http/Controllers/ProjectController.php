<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index( Request $request)
    {
        Controller::validatePermissions($request->user(),'GET','/projects');
        $user = $request->user();
         // refactor this validation in a second stage
        if($request->user()->role->access
            ->where('method','=','GET')
            ->where('endpoint','=','/allprojects')
            ->first()){
            $data=Project::select('projects.*','dpas.name as dpa')
                ->join('dpas','projects.cod_dpa','=','dpas.cod_dpa')
                ->get();
        }else{
            $data=Project::select('projects.*','dpas.name as dpa')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->join('dpas','projects.cod_dpa','=','dpas.cod_dpa')
                ->where('project_user.user_id','=',$request->user()->id)
                ->get();
        }

        return $this->response('false', Response::HTTP_OK, '200 OK', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->response('true', Response::HTTP_NOT_FOUND, '404 NOT FOUND');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Controller::validatePermissions($request->user(),'POST','/projects');

        $data=[];
        $edit_permission=[
            'name',
            'resolution',
            'detail',
            'image',
            'cod_dpa'
        ];

        foreach ($edit_permission as $d){
            if(isset($request->$d)){
                $data[$d]=$request->$d;
            }
        }

        $validate=\Validator::make($data,[
            'name'    => 'required',
            'resolution'    => 'required',
            'cod_dpa'    => 'exists:dpas,cod_dpa|required'
        ]);

        if ($validate->fails())
        {
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
        }

        $project=Project::create($data);

        $log="The user '".$request->user()->id."' create user '$request->id'";
        $this->log('info',$log,'web',$request->user());
        return $this->response(false, Response::HTTP_CREATED, '201 Created',$project);

    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request,$id)
    {
        Controller::validatePermissions($request->user(),'GET','/projects');
        // refactor this validation in a second stage
        if($request->user()->role->access
            ->where('method','=','GET')
            ->where('endpoint','=','/allprojects')
            ->first()||Project::findOrFail($id)
                ->join('project_user','project_user.project_cod_project','=','projects.cod_project')
                ->where('project_user.user_id','=',$request->user()->id)
                ->first()){
              $data=Project::select('projects.*','dpas.name as dpa')
                            ->join('dpas','projects.cod_dpa','=','dpas.cod_dpa')->findOrFail($id);

            return $this->response('false', Response::HTTP_OK, '200 OK', $data);
        }
        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->response('true', Response::HTTP_NOT_FOUND, '404 NOT FOUND');
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
        Controller::validatePermissions($request->user(),'PUT','/projects');
        $data=[];
        $edit_permission=[
            'name',
            'resolution',
            'detail',
            'image',
            'cod_dpa'
        ];

        foreach ($edit_permission as $d){
            if(isset($request->$d)){
                $data[$d]=$request->$d;
            }
        }

        $validate=\Validator::make($data,[
            'name'    => 'required',
            'resolution'    => 'required',
            'cod_dpa'    => 'exists:dpas,cod_dpa|required'
        ]);

        if ($validate->fails())
        {
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
        }
        $project=Project::findOrFail($id);

        if($project->update($data)){
            $log="The user '".$request->user()->id."' update project '$request->id'";
            $this->log('info',$log,'web',$request->user());
            return $this->response('false', Response::HTTP_OK, '200 OK', $project);
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
        Controller::validatePermissions($request->user(),'DELETE','/projects');
            try {
                Project::findOrFail($id)->delete();
                return $this->response('false', Response::HTTP_OK, '200 OK');
            }catch (\Exception $e){
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
            }
    }

    /**
     *  Add a new member to a project
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function addUsers(Request $request, $id){
        Controller::validatePermissions($request->user(),'POST','/projects/{project}/members');
        try {
            Project::findOrFail($id)->users()->attach($request->user_id);
            return $this->response('false', Response::HTTP_OK, '200 OK');
        }catch (\Exception $e){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }

    }

    /**
     * Remove a member to a project
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeUsers(Request $request, $id){
        Controller::validatePermissions($request->user(),'DELETE','/projects/{project}/members');
        try {
            Project::findOrFail($id)->users()->detach($request->user_id);
            return $this->response('false', Response::HTTP_OK, '200 OK');
        }catch (\Exception $e){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }

    }

    /**
     * Return members to project id
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request, $id){
        Controller::validatePermissions($request->user(),'PUT','/projects');
        $project=Project::findOrFail($id);
        $data=$project->users;
        $users=User::select('id','name','lastname','email')
            ->get();
        foreach ($users as $us){
            $us['member']=false;
            foreach ($data as $dt){
                if($dt->id==$us->id){
                    $us['member']=true;
                    unset($dt);
                }
            }
        }
        return $this->response('false', Response::HTTP_OK, '200 OK',$users);
    }
}
