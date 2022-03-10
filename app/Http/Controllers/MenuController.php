<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuController extends Controller
{


    public function navigation(Request $request){
        $user=$request->user();
        $menu=array();
        $me= $user::select('menu.*')
            ->join('access_role','access_role.cod_rol','=','users.cod_rol')
            ->join('access','access.cod_access','=','access_role.cod_access')
            ->rightjoin('menu','access.cod_menu','=','menu.cod_menu')
            ->groupBy('menu.cod_menu','menu.name','menu.order','menu.icon','menu.path','menu.cod_menu_parent','menu.created_at','menu.updated_at')
            ->get();
        $array = json_decode(json_encode($me), true);
        foreach ($array as $m){
            if(!isset($m['cod_menu_parent'])){
                foreach ($array as $ch){
                    if(!isset($m['children'])){
                        $m['children']=array();
                    }
                    if($ch['cod_menu_parent']==$m['cod_menu']){
                        $m['children'][]=$ch;
                        unset($ch);
                    }
                }
                $menu[]=$m;
            }

        }
        return $this->response('false', Response::HTTP_OK, '200 OK', $menu);
    }
}
