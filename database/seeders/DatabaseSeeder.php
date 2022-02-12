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

        DB::table('roles')->insert([
            "cod_rol"=>1,
            "name"=>'Administrator',
            "detail"=>null
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
