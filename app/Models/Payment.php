<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $fields;
    private $key;
    private $replaceSymbol;

    public function __construct($fields, $key, $replaceSymbol)
    {
       $this->fields = $fields;
       $this->replaceSymbol = $replaceSymbol;
       $this->key = $key;
    }

    private function sign_creating(){
        $signString = "";
        ksort($this->fields);
        foreach($this->fields as $k => $t){
            $signString .= $k.'='.$t.$this->replaceSymbol;
        }
        $signString .= $this->key;
        
        return $signString;
    }

    private function sign_md_hashing(){
        return md5($this->sign_creating());
    }

    private function sign_sh_hashing(){
        return hash('sha256', $this->sign_creating());
    }


    public function limiting($limit){
        if($limit == 0){
            exit("Достигнут лимит обработки оплат");
        }
    }

    public function send_json_payment($addSign = false, $callbaklUrl, $limit){
        $this->limiting($limit);
        if($addSign){
            $this->fields['sign'] = $this->sign_sh_hashing();
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $callbaklUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($this->fields),
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json",
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if($err){
            return "cURL Error #:" . $err;
        }else{
            $limit--;
            return (json_decode($response));
        }
    }

    public function send_form_payment($callbaklUrl, $limit){
        $this->limiting($limit);
        $auth = $this->sign_md_hashing();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $callbaklUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($this->fields),
            CURLOPT_HTTPHEADER => array(
                "content-type: multipart/form-data",
                "Authorization: $auth",
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if($err){
            return "cURL Error #:" . $err;
        }else{
            $limit--;
            return (json_decode($response));
        }
    }
}
