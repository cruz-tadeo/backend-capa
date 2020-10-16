<?php

namespace App\Http\Controllers;

use App\Bitacora;
use App\Helpers\JwtAuth;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function register(Request $request)
    {
                $request->validate([
                    'name' => 'required|string',
                    'apellido_paterno'=>'required|string',
                    'apellido_materno'=>'required|string',
                    'email' => 'required|email',
                    'celular'=>'required',
                    'password' => 'required'
                ]);

                $user = new User();
                $user->assignRole('cliente');
                $user->name = $request->name;
                $user->email = $request->email;
                $user->celular = $request->celular;
                $user->apellido_paterno = $request->apellido_paterno;
                $user->apellido_materno = $request->apellido_materno;
                $user->role = null;
                $pwd = Hash::make($request->password);
                $user->password = $pwd;
                $user->code = '+52';

                $isset_user = User::where('celular','=',$request->celular)->get();
                if (count($isset_user) === 0 ){
                    $user->save();
                    $role = $user->getRoleNames()->first();
                    $user->role = $role;
                    $user->save();
                    $bitacora = new Bitacora();
                    $bitacora->tabla = 'usuarios';
                    $bitacora->operacion = "CREAR";
                    $bitacora->fk_usuario = $user->id;
                    $bitacora->descripcion = "{"."id:".$user->id.","."name:,".$user->name.","."apellido_paterno:,".$user->apellido_paterno.","."apellido_materno:".$user->apellido_materno.","."email:,".$user->email.","."celular:,".$user->celular.","."role:,".$user->role.","."password:,".$user->password.","."}";
                    $bitacora->eliminado = false;
                    $bitacora->save();
                    $data = array(
                        'status'=>'success',
                        'code'=>200,
                        'message'=>'usuario registrado correctamente'
                    );
                }else{
                    $data = array(
                        'status'=>'error',
                        'code'=>400,
                        'message'=>'Usuario duplicado, no puede registrarse'
                    );
                }

        return response()->json($data,200);
    }

    public function login(Request $request)
    {
        if(!Auth::attempt($request->only('celular', 'password'))){
            return response()->json([
                'message'=>'Unauthorized'
            ],401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'status'=>'success',
            'access_token'=>$tokenResult->accessToken,
            'token_type'=>'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDayDateTimeString(),
            'user'=>$user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'status'=>'success',
            'message'=>'Cierre de sesion satisfactorio'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'ok'=>'true',
            'user'=>$request->user()
        ]);
    }

}
