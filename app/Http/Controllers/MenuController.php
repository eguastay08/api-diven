<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuController extends Controller
{


    public function navigation(Request $request){
        $user=$request->user();
        $menu=array();

         $me = Role::select('menu.name','menu.order','menu.icon','menu.path','menu.cod_menu_parent','menu.cod_menu')
            ->join('access_role','access_role.cod_rol','=','roles.cod_rol')
            ->join('access','access.cod_access','=','access_role.cod_access')
            ->rightjoin('menu','access.cod_menu','=','menu.cod_menu')
            ->where('roles.cod_rol','=',$user->cod_rol)
            ->groupBy('menu.name','menu.order','menu.icon','menu.path','menu.cod_menu_parent','menu.cod_menu')
            ->get();

        $array = json_decode(json_encode($me), true);
        foreach ($array as $ar){
             if(isset($ar['cod_menu_parent'])){
                 if(!array_search($ar['cod_menu_parent'], array_column($array , 'cod_menu'))){
                     $par=Menu::where('cod_menu','=',$ar['cod_menu_parent'])->first();
                     $array[] = json_decode(json_encode($par), true);
                 }
             }
        }

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
