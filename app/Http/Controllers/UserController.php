<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Controller::validatePermissions($request->user(),'GET','/users');
        $search= $request->search ?? '';
        $data = User::select('users.*','roles.name as rol')
                ->join('roles','users.cod_rol','=','roles.cod_rol')
                ->orwhere('users.name','like','%'.$search.'%')
                ->orwhere('users.lastname','like','%'.$search.'%')
                ->orwhere('users.email','like','%'.$search.'%')
                ->get();
        foreach ($data as $d){
            $this->generateAvatarUrl($d);
        }
        return $this->response('false', Response::HTTP_OK, '200 OK', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Controller::validatePermissions($request->user(),'POST','/users');
        $data=[];
        $edit_permission=[
            'name',
            'lastname',
            'email',
            'gender',
            'password',
            'photography',
            'cod_rol'
        ];

        foreach ($edit_permission as $d){
            if(isset($request->$d)){
                $data[$d]=$request->$d;
            }
        }

        $validate=\Validator::make($data,[
            'name'    => 'required',
            'lastname'    => 'required',
            'email'    => 'email|unique:users|required',
            'gender'=>'in:male,female,other|required',
            'password'=>'required',
            'cod_rol'=>'exists:roles,cod_rol|required'
        ]);

        if ($validate->fails())
        {
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
        }
        $data['password']=bcrypt($data['password']);

        $user = User::create($data);
        $log="The user '".$request->user()->id."' create user '$user->id'";
        $this->log('info',$log,'web',$request->user());
        return $this->response(false, Response::HTTP_CREATED, '201 Created',$user);
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
        Controller::validatePermissions($request->user(),'GET','/users');
        try {
            $data=User::findOrFail($id);
            $data['role']=Role::find($data->cod_rol);
            $this->generateAvatarUrl($data);
            unset($data->cod_rol);
            return $this->response('false', Response::HTTP_OK, '200 OK',$data);
        }catch (\Exception $e){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }
    }

    /**
     * Return information user logged
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $data=$request->user();
        $data['access']=Role::find($data->cod_rol)->access;
        $this->generateAvatarUrl($data);
        try {

            unset($data->cod_rol);
            return $this->response('false', Response::HTTP_OK, '200 OK',$data);
        }catch (\Exception $e){
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        return $this->response('true', Response::HTTP_NOT_FOUND, '404 NOT FOUND');
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
        Controller::validatePermissions($request->user(),'PUT','/users');
        $user=User::findOrFail($id);
        $data=[];
        $edit_permission=[
            'name',
            'lastname',
            'gender',
            'password',
            'email',
            'photography',
            'cod_rol',
            'active'
        ];

        foreach ($edit_permission as $d){
            if(isset($request->$d)){
                $data[$d]=$request->$d;
            }
        }

        $validate=\Validator::make($data,[
            'name'    => 'required',
            'lastname'    => 'required',
            'email'    => 'email',
            'gender'=>'in:male,female,other',
            'cod_rol'=>'exists:roles,cod_rol',
            'active'=>'boolean'
        ]);
        if ($validate->fails())
        {
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
        }
        if(isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        if($user->update($data)){
            return $this->response('false', Response::HTTP_OK, '200 OK', $user);
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
        Controller::validatePermissions($request->user(),'DELETE','/users');
        if($request->user()->id!=$id){
            try {
                User::findOrFail($id)->delete();
                return $this->response('false', Response::HTTP_OK, '200 OK');
            }catch (\Exception $e){
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
            }
        }
        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
    }

    /**
     * Generate avatar url using gravatar or local storage
     *
     * @param User $d
     */
    private function generateAvatarUrl(User $d){
        $data['photography']=$d->photography;
        $validate=\Validator::make($data,[
            'photography'=>'active_url'
        ]);

        if (!$validate->fails()){
            return;
        }

        if(isset($d->photography)){

            $image=File::find($d->photography);
            $uri='/api/v1/image';
            $url=env('APP_URL').$uri."/".$image['name'];
            $d->photography=$url;
        }else{
            $email = $d->email;
            $grav_url = "https://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) );
            $d->photography=$grav_url;
        }
    }
}
