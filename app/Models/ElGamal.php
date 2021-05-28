<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;

class ElGamal extends Model
{
    use HasFactory;

    function __construct($input){

        //key set

    	$this->X = isset(json_decode(base64_decode($input['private_key'], true))->X) ? json_decode(base64_decode($input['private_key'], true))->X : '';

        $this->Y = isset(json_decode(base64_decode($input['private_key'], true))->Y) ? json_decode(base64_decode($input['private_key'], true))->Y : '';

        $this->K = isset(json_decode(base64_decode($input['private_key'], true))->K) ? json_decode(base64_decode($input['private_key'], true))->K : '';

    	$this->P =isset(json_decode(base64_decode($input['public_key'], true))->P)? json_decode(base64_decode($input['public_key'], true))->P : '';

        $this->A = isset(json_decode(base64_decode($input['public_key'], true))->A)? json_decode(base64_decode($input['public_key'], true))->A : '';

        $this->D = isset(json_decode(base64_decode($input['public_key'], true))->D)? json_decode(base64_decode($input['public_key'], true))->D : '';

        $this->private_key = $input['private_key'];

        $this->public_key = $input['public_key'];

        //encrypt and send

        $this->encrypt_doc = $input['encrypt_doc'];

        $this->decrypt_doc = $input['decrypt_doc'];

        $this->encrypt_md5 = $input['encrypt_md5'];

        $this->encrypt_encrypted_doc = $input['encrypt_encrypted_doc']; // ban ma hoa gui di

        $this->decrypt_encrypted_doc = $input['decrypt_encrypted_doc']; // ban ma hoa nhan duoc

        $this->decrypt_decrypted_doc= $input['decrypt_decrypted_doc']; // ban giai ma duoc tu ban ma hoa nhan duoc

    }

    public function kiemtrasonguyento($so){

    	$kiemtra = true;

    	if ($so == 2 || $so == 3){

            return $kiemtra;
        }
        else{
            if ($so == 1 || $so % 2 == 0 || $so % 3 == 0){

                $kiemtra = false;
            }
            else{

                for ($i = 5; $i <= sqrt($so); $i = $i + 6)

                    if ($so % $i == 0 || $so % ($i + 2) == 0){

                        $kiemtra = false;

                        break;
                    }
            }
        }
        return $kiemtra;
    }
    public function nguyentocungnhau($so1, $so2)
        {

            while ($so2 != 0)
            {
                $temp = $so1 % $so2;
                $so1 = $so2;
                $so2 = $temp;
            }

            if ($so1 == 1) 
            	$ktx_ = true;
            else 
            	$ktx_ = false;

            return $ktx_;
        }

    public function RSA_mod ($mx, $ex, $nx){
        if(!is_numeric($mx) || !is_numeric($ex) || !is_numeric($nx)){
            throw new Exception('Số sai');
        }
            //bình phương và nhân
            //Chuyển e sang hệ nhị phân
                $a = [];
                $k = 0;
                do{
                    $a[$k] = $ex % 2;
                    $k++;
                    $ex = $ex / 2;
                }while ($ex != 0);
                //Quá trình lấy dư

                $kq = 1;
                for ($i = $k - 1; $i >= 0; $i--){

                    $kq = ($kq * $kq) % $nx;
                    if ($a[$i] == 1){
                        $kq = ($kq * $mx) % $nx;
                    }

                }
                return $kq;
            
    }
    public function kiemTraUocCuaSoP($so_P, $so_Q)
        {
            $kt_Okie = true;
            if (($so_P - 1) % $so_Q == 0)
            {
                $kt_Okie = true;
            }
            else
                $kt_Okie = false;
            return $kt_Okie;
        }
        private function kiemTraPTSinh($so_kt, $E_SoP_, $E_soQ_)// kiem tra phan tu sinh
        {
            $ktOkie = true;

            $soMu = ($E_SoP_ - 1) / $E_soQ_;

            $ketQuaKT = $this->LuyThuaModulo_($so_kt, $soMu, $E_SoP_);

            if ($ketQuaKT != 1)
            {
                $ktOkie = true;
            }
            else
            {
                if ($ketQuaKT == 1) 
                    $ktOkie = false;
            }

            return $ktOkie;
        }
        public function LuyThuaModulo_($CoSo_, $SoMu_, $soModulo_)
        {

            //Sử dụng thuật toán "bình phương nhân"
            //Chuyển e sang hệ nhị phân
            $a = [];

            $k = 0;

            do
            {
                $a[$k] = $SoMu_ % 2;

                $k++;

                $SoMu_ = $SoMu_ / 2;

            }while ($SoMu_ != 0);

            //Quá trình lấy dư
            $kq = 1;

            for ($i = $k - 1; $i >= 0; $i--)
            {
                $kq = ($kq * $kq) % $soModulo_;

                if ($a[$i] == 1)

                    $kq = ($kq * $CoSo_) % $soModulo_;
            }
            return $kq;
        }
    public function taokhoa()
        {
            // chọn số nguyên tố ngẫu nhiên Q thỏa mãn Q là ước của P - 1;
            do
            {
                $this->Q = rand(2, $this->P-1);
            }
            while (!$this->kiemtrasonguyento($this->P) || !$this->kiemTraUocCuaSoP($this->P, $this->Q));



            // tìm số G để tìm số A (A là phần tử sinh): 
            do
            {
                $this->G_A = rand(2, $this->P - 1);
            }
            while (!$this->kiemTraPTSinh($this->G_A, $this->P, $this->Q));


            $this->A = $this->LuyThuaModulo_($this->G_A, ($this->P - 1) / $this->Q, $this->P); // phần tử sinh

            do
            {
                $this->X = rand(2, $this->P - 2);
            }
            while ($this->X == $this->Q || $this->X == $this->G_A);

            // d= a^x mod P
            $this->D = $this->LuyThuaModulo_($this->A, $this->X, $this->P);// beta; d          
            do
            {
                $this->K = rand(2, $this->P - 2);
            }
            while ($this->K == $this->X || $this->K == $this->A || $this->K == $this->Q || $this->K == $this->G_A || !$this->nguyenToCungNhau($this->K, $this->P - 1));

            // Tính Y = A^k mod p - Khóa công khai

            $this->Y = $this->LuyThuaModulo_($this->A, $this->K, $this->P);

            $this->private_key = base64_encode(json_encode(['X'=>$this->X,'Y'=>$this->Y,'K'=>$this->K]));

            $this->public_key = base64_encode(json_encode(['P'=>$this->P,'A'=>$this->A,'D'=>$this->D]));


        }   

    public function khoitao(){

    	do{

    		$this->P = rand(255, 400);

    	}while(!$this->kiemtrasonguyento($this->P));

    	   $this->taokhoa();

        return true;
    }

    public function mahoa()
    {
        if(trim($this->encrypt_doc) == "")
            return false;
        try{
                $base64 = $this->encrypt_md5;

                $mh_temp2 = [];

                for ($i = 0; $i < strlen($base64); $i++)
                {
                    $mh_temp2[$i] = ord($base64[$i]); //return integer
                }

                        $mh_temp3 = [];

                        for ($i = 0; $i < count($mh_temp2); $i++)
                        {
                            $mh_temp3[$i] = (($mh_temp2[$i] % $this->P) * ($this->LuyThuaModulo_($this->D, $this->K, $this->P))) % $this->P;

                            if($mh_temp3[$i] < 0) return false;
                        }

                        $data = implode(',',$mh_temp3);

                $this->encrypt_encrypted_doc = base64_encode($data);
                
                return true;
        }
        catch(Exception $e){return false;}
    }
    public function check()
        {
            try{

                    $giaima = base64_decode($this->decrypt_encrypted_doc);

                    $b = explode(',',$giaima);           

                    $c = [];

                    $r = $this->LuyThuaModulo_($this->Y, ($this->P - (1 + $this->X)), $this->P);
                    //dd($this->P);
                    for ($i = 0; $i < count($b); $i++)
                    {

                        $c[$i] = ($b[$i] * $r) % $this->P;// giải mã

                        if($c[$i] < 0) {
                            $this->decrypt_decrypted_doc = "";
                            return false;

                        }
                    }

                    $str = "";

                    for ($i = 0; $i < count($c); $i++)

                    {
                        $str .= chr($c[$i]);
                    }

                    $this->decrypt_decrypted_doc = $str;

                    if($this->decrypt_decrypted_doc == md5($this->decrypt_doc))

                        return true;

                    else
                        
                        return false;
                
            }catch(Exception $e){

                $this->decrypt_decrypted_doc = "";
                return false;

            }
        }
}
