<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ElGamal;

class indexController extends Controller
{

    public function generateKey(Request $req){

    	$rsa = new ElGamal($req->all());

    	if($rsa->khoitao())

           return response(["error"=>0,"message" => "Tạo khóa thành công","data" => $rsa->toArray()]);

        return response(["error"=>1,"message" => "Tạo khóa không thành công","data" => $rsa->toArray()]);
    }

    public function encrypt(Request $req){

    	$rsa = new ElGamal($req->all());

        if($rsa->mahoa()){

           

            return response(["error"=>0,"message" => "Mã hóa thành công","data" => $rsa->toArray()]);
        }
        return response(["error"=>1,"message" => "Mã hóa thất bại","data" => $rsa->toArray()]);
    }

    public function check(Request $req){

    	$rsa = new ElGamal($req->all());

    	if($rsa->check())

            return response(["error"=>0,"message"=>"Chữ ký hợp lệ","data" => $rsa->toArray()]);

        return response(["error"=>1,"message" => "Văn bản đã được chỉnh sửa hoặc chữ ký không chính
xác","data" => $rsa->toArray()]);
    }

    public function index(Request $req){

    	return view('welcome');
    }
}
