<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();



        DB::table('menu')->insert([
            'cod_menu'=>'1',
            'name'=>'Administrar Usuarios',
            'order'=>'0',
            'icon'=>'user',
            'path'=>null,
            'cod_menu_parent'=>null
        ]);

        DB::table('menu')->insert([
            'cod_menu'=>'2',
            'name'=>'Roles',
            'order'=>'1',
            'icon'=>null,
            'path'=>'/roles',
            'cod_menu_parent'=>'1'
        ]);

        DB::table('menu')->insert([
            'cod_menu'=>'3',
            'name'=>'Administrar Proyectos',
            'order'=>'3',
            'icon'=>null,
            'path'=>'/projects',
            'cod_menu_parent'=>null
        ]);
        DB::table('menu')->insert([
            'cod_menu'=>'4',
            'name'=>'Usuarios',
            'order'=>'3',
            'icon'=>null,
            'path'=>'/users',
            'cod_menu_parent'=>1
        ]);

        DB::table('roles')->insert([
            "cod_rol"=>1,
            "name"=>'Administrator',
            "detail"=>null
        ]);

        DB::table('access')->insert([
            'cod_access'=>1,
            'name'=>'Get all users',
            'endpoint'=>'/users',
            'method'=>'GET',
            'cod_menu'=>4
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>1,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>2,
            'name'=>'Register new user',
            'endpoint'=>'/users',
            'method'=>'POST',
            'cod_menu'=>4
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>2,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>3,
            'name'=>'update user',
            'endpoint'=>'/users',
            'method'=>'POST',
            'cod_menu'=>4
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>3,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>4,
            'name'=>'delete user',
            'endpoint'=>'/users',
            'method'=>'DELETE',
            'cod_menu'=>4
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>4,
            'cod_rol'=>1
        ]);
        /**ACCESS ROLES**/
        DB::table('access')->insert([
            'cod_access'=>5,
            'name'=>'Get All Roles',
            'endpoint'=>'/roles',
            'method'=>'GET',
            'cod_menu'=>2
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>5,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>6,
            'name'=>'Insert New Role',
            'endpoint'=>'/roles',
            'method'=>'POST',
            'cod_menu'=>2
        ]);



        DB::table('access_role')->insert([
            'cod_access'=>6,
            'cod_rol'=>1
        ]);


        DB::table('access')->insert([
            'cod_access'=>7,
            'name'=>'Update Role',
            'endpoint'=>'/roles',
            'method'=>'PUT',
            'cod_menu'=>2
        ]);



        DB::table('access_role')->insert([
            'cod_access'=>7,
            'cod_rol'=>1
        ]);


        DB::table('access')->insert([
            'cod_access'=>8,
            'name'=>'DELETE Role',
            'endpoint'=>'/roles',
            'method'=>'DELETE',
            'cod_menu'=>2
        ]);



        DB::table('access_role')->insert([
            'cod_access'=>8,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>9,
            'name'=>'GET ALL ACCESS',
            'endpoint'=>'/access',
            'method'=>'GET',
            'cod_menu'=>2
        ]);



        DB::table('access_role')->insert([
            'cod_access'=>9,
            'cod_rol'=>1
        ]);

        /*PROJECTS*/

        DB::table('access')->insert([
            'cod_access'=>10,
            'name'=>'GET MY PROJECTS',
            'endpoint'=>'/projects',
            'method'=>'GET',
            'cod_menu'=>3
        ]);



        DB::table('access_role')->insert([
            'cod_access'=>10,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>11,
            'name'=>'GET ALL PROJECTS',
            'endpoint'=>'/allprojects',
            'method'=>'GET',
            'cod_menu'=>3
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>11,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>12,
            'name'=>'CREATE PROJECT',
            'endpoint'=>'/projects',
            'method'=>'POST',
            'cod_menu'=>3
        ]);



        DB::table('access_role')->insert([
            'cod_access'=>12,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>13,
            'name'=>'UPDATE PROJECT',
            'endpoint'=>'/projects',
            'method'=>'PUT',
            'cod_menu'=>3
        ]);



        DB::table('access_role')->insert([
            'cod_access'=>13,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>14,
            'name'=>'DELETE PROJECT',
            'endpoint'=>'/projects',
            'method'=>'DELETE',
            'cod_menu'=>3
        ]);



        DB::table('access_role')->insert([
            'cod_access'=>14,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>15,
            'name'=>'ADD NEW MEMBERS A PROJECT',
            'endpoint'=>'/projects/{project}/members',
            'method'=>'POST',
            'cod_menu'=>3
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>15,
            'cod_rol'=>1
        ]);


        DB::table('access')->insert([
            'cod_access'=>16,
            'name'=>'REMOVE MEMBERS A PROJECT',
            'endpoint'=>'/projects/{project}/members',
            'method'=>'DELETE',
            'cod_menu'=>3
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>16,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>17,
            'name'=>'GET SURVEYS FROM A PROJECT',
            'endpoint'=>'/projects/{project}/surveys',
            'method'=>'GET',
            'cod_menu'=>3
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>17,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>18,
            'name'=>'CREATE SURVEYS FROM A PROJECT',
            'endpoint'=>'/projects/{project}/surveys',
            'method'=>'POST',
            'cod_menu'=>3
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>18,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>19,
            'name'=>'UPDATE SURVEYS',
            'endpoint'=>'/surveys',
            'method'=>'PUT',
            'cod_menu'=>3
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>19,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>20,
            'name'=>'DELETE SURVEYS',
            'endpoint'=>'/surveys',
            'method'=>'DELETE',
            'cod_menu'=>3
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>21,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>22,
            'name'=>'CREATE ANSWERS',
            'endpoint'=>'/answers',
            'method'=>'POST',
            'cod_menu'=>3
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>22,
            'cod_rol'=>1
        ]);

        DB::table('access')->insert([
            'cod_access'=>23,
            'name'=>'DELETE ANSWERS',
            'endpoint'=>'/answers',
            'method'=>'DELETE',
            'cod_menu'=>3
        ]);

        DB::table('access_role')->insert([
            'cod_access'=>23,
            'cod_rol'=>1
        ]);

        DB::table('users')->insert([
            'name'=>'admin',
            'lastname'=>'admin',
            'email'=>'admin@ueb.edu.ec',
            'gender'=>'other',
            'password'=>password_hash('12345',PASSWORD_DEFAULT),
            'photography'=>null,
            'remember_token'=>null,
            'email_verified_at' => null,
            'cod_rol'=>1
        ]);

        DB::table('projects')->insert([
            'cod_project'=>1,
            'name'=>'Demo',
            'resolution'=>'DEMO-001-23',
            'detail'=>'This project is a demonstration carried out in the parish of Facundo Vela.',
            'image'=>null,
            'cod_dpa'=>340
        ]);

        DB::table('surveys')->insert([
            'cod_survey'=>'1',
            'name'=>'survey demo 1',
            'date_init'=>'2022-02-09 00:00:00',
            'date_finally'=>'2022-03-31 16:46:27',
            'status'=>0,
            'detail'=>null,
            'cod_project'=>'1'
        ]);

    }
}
