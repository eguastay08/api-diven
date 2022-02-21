<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Controller::validatePermissions($request->user(),'GET','/roles');
        $data=Role::get();
        return $this->response('false', Response::HTTP_OK, '200 OK', $data);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Controller::validatePermissions($request->user(),'POST','/roles');
        $data=[];
        $edit_permission=[
            "name",
            "detail"
        ];

        foreach ($edit_permission as $d){
            if(isset($request->$d)){
                $data[$d]=$request->$d;
            }
        }
        $validate=\Validator::make($data,[
            'name'    => 'required|unique:roles',
        ]);

        if ($validate->fails())
        {
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
        }

        $role = Role::create($data);
        $log="The user '".$request->user()->id."' create role '$role->cod_rol'";
        $this->log('info',$log,'web',$request->user());
        return $this->response(false, Response::HTTP_CREATED, '201 Created',$role);
    }


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        Controller::validatePermissions($request->user(),'GET','/roles');
        try {
            $data=Role::findOrFail($id);
            $data['access']=$access=$data->access;
            return $this->response('false', Response::HTTP_OK, '200 OK',$data);
        }catch (\Exception $e){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }
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
        Controller::validatePermissions($request->user(),'PUT','/roles');
        $role=Role::findOrFail($id);
        $edit_permission=[
            "detail"
        ];

        foreach ($edit_permission as $d){
            if(isset($request->$d)){
                $data[$d]=$request->$d;
            }
        }

        if($role->update($data)){
            return $this->response('false', Response::HTTP_OK, '200 OK', $role);
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
        Controller::validatePermissions($request->user(),'DELETE','/roles');

            try {
                Role::findOrFail($id)->delete();
                return $this->response('false', Response::HTTP_OK, '200 OK');
            }catch (\Exception $e){
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
            }
    }

    /**
     * Set new access to role
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAccess(Request $request, $id){
        try {
            Controller::validatePermissions($request->user(),'PUT','/roles');
            $access['cod_access']=$request->cod_access;
            $validate=\Validator::make( $access,[
                'cod_access'    => 'required',
            ]);
            if ($validate->fails())
            {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }
            $role=Role::findOrFail($id);
            $role->access()->attach($access['cod_access']);
            return $this->response('false', Response::HTTP_OK, '200 OK');
        }catch (\Exception $e){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }
    }

    /**
     * Remove access to role
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeAccess(Request $request, $id){
        try {
            Controller::validatePermissions($request->user(),'PUT','/roles');
            $access['cod_access']=$request->cod_access;
            $validate=\Validator::make( $access,[
                'cod_access'    => 'required',
            ]);
            if ($validate->fails())
            {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }
            $role=Role::findOrFail($id);
            $role->access()->detach($access['cod_access']);
            return $this->response('false', Response::HTTP_OK, '200 OK');
        }catch (\Exception $e){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }
    }
}
