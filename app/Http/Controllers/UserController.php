<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller {

    public function prueba(Request $request) {
        return "Accion de prueba User Controller";
    }

    public function register(Request $request) {
        //{"name":"Luis","surname":"Romero","email":"luis@luis.com","password":"1234"}

        $json = $request->input('json', null);

        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //JSON bien formado
        if (!empty($params) && !empty($params_array)) {

            $params_array = array_map('trim', $params_array);

            //Recoger los datos del usuario por POST
            //Validar datos
            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'surname' => 'required|alpha',
                        'email' => 'required|email|unique:users',
                        'password' => 'required|alpha'
            ]);
            //Fallo en la validacion
            if ($validate->fails()) {

                $data = array(
                    'status' => 'error',
                    'code' => '404',
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
                //Validacion hecha con exito    
            } else {

                //Instancia del nuevo usuario
                //$pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);
                $pwd = hash('sha256', $params->password);
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';


                //guardar datos

                $user->save();


                $data = array(
                    'status' => 'success',
                    'code' => '200',
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }

            //Cifrar la contraseña
            //Comprobar si el usuario existe(duplicado)
            //Crear el usuario
            //JSON mal formado    
        } else {
            $data = array(
                'status' => 'error',
                'code' => '404',
                'message' => 'No se han introducido los datos',
            );
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {
        $jwtAuth = new \JwtAuth;
        //Recibir por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);





        //Validar los datos

        $validate = \Validator::make($params_array, [
                    'email' => 'required|email',
                    'password' => 'required|alpha'
        ]);
        //Fallo en la validacion
        if ($validate->fails()) {

            $signup = array(
                'status' => 'error',
                'code' => '404',
                'message' => 'El usuario no se podido logear',
                'errors' => $validate->errors()
            );
            //Validacion hecha con exito    
        } else {
            //Cifrar la password   
            $pwd = hash('sha256', $params->password);

            //devolver el token o datos
            $signup = $jwtAuth->signup($params->email, $pwd);
            if (!empty($params->gettoken)) {
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }



        /* $email='fujoshi@gmail.com';
          $password='patata';
          $pwd = hash('sha256',$password); */

        // return $jwtAuth->signup($email,$pwd);
        //  return response()->json($jwtAuth->signup($email,$pwd,true),200);
        return response()->json($signup, 200);
    }

    public function update(Request $request) {

        //Comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        //recoger los datos por post
        $json = $request->input('json', null);

        $params_array = json_decode($json, true);

        if ($checkToken && !empty($params_array)) {

            //Sacar usuario identificado para que al actualizar el email no tenga que ser requerido

            $user = $jwtAuth->checkToken($token, true);


            //validar los datos

            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'surname' => 'required|alpha',
                        'email' => 'required|email|unique:users,' . $user->sub
            ]);

            //quitar los campos que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            //actualizar el usuario en la base de datos
            $user_update = User::where('id', $user->sub)->update($params_array);


            //devolver array con el resultado

            $data = array(
                'status' => 'success',
                'code' => '200',
                'message' => 'El usuario se ha sido actualizado correctamente',
                'user' => $user,
                'changes' => $params_array
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El Usuario no esta identificado correctamente.'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function upload(Request $request) {

        //Recoger datos de la peticion

        $image = $request->file('file0');

        //Validar imagen

        $validate = \Validator::make($request->all(), [
                    'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
        if (!$image || $validate->fails()) {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen.'
            );
        } else {
            //Subir la imagen (se crea una especie de disco virtual o carpeta en laravel)
            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));


            $data = array(
                'image' => $image_name,
                'code' => 200,
                'status' => 'success'
            );
        }

        //Devolver el resultado
        //return response($data, $data['code'])->header('Content-Type','text/plain');

        return response()->json($data, $data['code']);
    }
    
    public function getImage($filename){
        
        $isset=\Storage::disk('users')->exists($filename);
        
        if($isset){
        $file = \Storage::disk('users')->get($filename);
        
        return Response($file,200);
        }else{
            
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha encontrado la imagen.'
            );
            
            return response()->json($data,$data['code']);
            
        }
    }
    
    
    public function detail($id){
        $user= User::find($id);
        if(is_object($user)){
           $data = array(
                'code' => 200,
                'status' => 'success',
               'user' => $user
            ); 
            
        }else{
                $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no existe.'
            );
        }
        
        return response()->json($data,$data['code']);
        
    }

}
