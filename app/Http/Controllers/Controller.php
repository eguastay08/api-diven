<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Response;
use PHPUnit\Util\Exception;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     *
     * Stores user or system log
     *
     * @param $type
     * @param $logt
     * @param $origin
     * @param User|null $user
     */
    public function log($type,$logt,$origin,User $user=null){
        $log=[];
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            $log['user_agent']=$_SERVER['HTTP_USER_AGENT'];
            $log['ip']=$_SERVER['REMOTE_ADDR'];
        }
        $log['type']=$type;
        $log['log']=$logt;
        $log['origin']=$origin;

        if($user!=null){
            $log['id_user']=$user->id;
        }
        Log::create($log);
    }

    /**
     *
     * standard for data return
     *
     * @param bool $error
     * @param $code
     * @param string $status
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($error=true, $code=Response::HTTP_NOT_FOUND, $status='404 Not Found', $data =array()){

        return response()->json([
            'error' => $error,
            'code' => $code,
            'status'=> $status,
            'data' => $data
        ], $code);
    }

    public function validatePermissions(User $user, $method, $endpoint){
         $access=$user->role->access
            ->where('method','=',$method)
            ->where('endpoint','=',$endpoint)
            ->first();
       if(is_null($access))
           abort($this->response(true,Response::HTTP_FORBIDDEN,'403 Forbidden' ));
    }

}
