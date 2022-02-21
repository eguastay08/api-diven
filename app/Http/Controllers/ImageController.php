<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request){
        Controller::validatePermissions($request->user(),'POST','/projects/{project}/surveys');
            if($request->hasFile('file')){
                $file = $request->file('file');
                $validate = \Validator::make(
                    array(
                        'file' => $file,
                    ),
                    array(
                        'file' => 'mimes:jpg,jpeg,png,svg'
                    )
                );

                if(!$validate->fails()){
                    $id=$request->user()->id;
                    $extension= $file->getClientOriginalExtension();
                    $type=$file->getType();
                    $path = $request->file('file')->store("file/$id/images");
                    $aux=explode('/',$path);
                    $name=end($aux);
                    $data=[
                        'id_user'=>$request->user()->id,
                        'path'=>$path,
                        'name'=>$name,
                        'extension'=>$extension,
                        'type'=>$type
                    ];
                    $file= File::create($data);
                    return $this->response('false', Response::HTTP_CREATED, '201 CREATED', $file);

                }else{
                    return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
                }
            }else{
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST',['errors'=>'file required']);
            }
    }

    /**
     * view image from storage
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function viewImage(Request $request)
    {
        $name = $request->img;
        $image = File::where('name', '=', $name)->first();
        if ($image) {
            $path = $image['path'];
            $route = env('APP_LOCATION') . '/storage/app/' . $path;
            if (Storage::exists($path)) {
                header("Content-type: image/jpeg");
                header("Content-length: " . filesize($route));
                header("Content-Disposition: inline; filename=" . $image['name']);
                readfile($route);
            } else {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
            }
        } else {
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
        }
    }

}
