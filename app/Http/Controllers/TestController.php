<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function Index(){
       return response()->json([
           'msg'=>'It returns response data'
       ]);
    }

    public function Post(){
        return response()->join([
            'msg'=> 'It is working'
        ]);
    }
}
