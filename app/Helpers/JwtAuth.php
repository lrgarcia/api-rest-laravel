<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth {
    
    public $key;
    
    
    public function __construct(){
        $this->key='esta_clave_es_secreta_+_**aaaa';
    }
    
    public function signup($email,$password,$getToken=null){
        //Buscar si exizte el usuario con sus credenciales
        $user= User::where([
            'email'=>$email,
            'password'=>$password
            
        ])->first();
        
        $signup=false;
        
        //Comprobar si son correctas(si nos devuelve un objeto)
        if(is_object($user)){
            $signup=true;
        }
        
        if($signup){
            //Generar el token con los datos del usuario identificado
            $token=array(
                'sub'         =>      $user->id,
                'email'       =>      $user->email,
                'name'        =>      $user->name,
                'surname'     =>      $user->surname,
                'lat'         =>          time(),
                'exp'         =>        time()+(7*24*60*60)//Dentro de una semana caduca este token 
            );
            
            $jwt= JWT::encode($token, $this->key,'HS256');
            $decoded=JWT::decode($jwt, $this->key,['HS256']);
           //Devolver los datos decodificados o el token en funcion del parámetro
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data =$decoded;
            }
            
        }else{
            $data=array(
                'status'=>'error',
                'message'=>'login incorrecto'
            );
        }
        
        return $data; 
        
    }
    //Comprueba si el token es correcto
    public function checkToken($jwt,$getIdentity=false){
        $auth=false;
        try{
            
        $jwt=str_replace('"','',$jwt);    
        $decoded=JWT::decode($jwt, $this->key,['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth=false;
            
        }catch(\DomainException $e){
            $auth=false;
        }   
         
        if(!empty($decoded)&& is_object($decoded)&&isset($decoded->sub)){
            $auth=true;
            
        }else{
            $auth=false;
        }
        
        if($getIdentity){
            return $decoded;
        }
        
        return $auth;
        }
    
    
    
   
    
    
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

