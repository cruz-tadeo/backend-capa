<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{
    public $key;

    public function __construct()
    {
        $this->key = 'esta-es-mi-clave-de-capa-78383627356765576565';
    }

    public function singup($celular,$password,$getToken = null)
    {
        $user = User::where(array(
            'celular'=>$celular,
            'password'=>$password
        ))->first();

        $singup = false;
        if(is_object($user)){
            $singup = true;
        }
        if($singup){
            //generar Token
            $token = array(
                'id'=> $user->id,
                'email'=>$user->email,
                'name'=>$user->name,
                'apellidos'=>$user->apellidos,
                'celular'=>$user->celular,
                'role'=>$user->role,
                'iat'=> time(),
                'exp'=> time() + (7 * 24 * 60 * 60)
            );
            $jwt = JWT::encode($token, $this->key, 'HS256' );
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));

            if(is_null($getToken)){
                return $jwt;
            }else{
                return $decoded;
            }
        }else{
            //devolver un error
            return array('status'=>'error','message'=>'Login ha fallado');
        }
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;

        try {
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));

        }catch (\UnexpectedValueException $e){
            $auth = false;
        }catch (\DomainException $e){
            $auth = false;
        }

        if(isset($decoded) && is_object($decoded) && isset($decoded->id)){
            $auth = true;
        }else{
            $auth = false;
        }

        if($getIdentity){
            return $decoded;
        }

        return $auth;
    }
}
