<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request){
        // Valida los datos en la peticion
        // Para confirmar el password es necesario agregar un campo password_confirmation
        $validator = Validator::make($request->all(), [
            "name" => "required|max:255",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed"
        ]);

        // Si la validación falla
        if($validator->fails()){
            // Devuelve mensaje y Http 422 - No se cumple con el formato solicitado
            return response()->json("La información no cumple con el formato solicitado", 422);
        }

        // Crea el usuario
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password
        ]);

        // Si no se puede crear el usuario
        if(!$user){
            // Devuelve mensaje Http 500 - Error interno del servidor
            return response()->json("No se pudo crear el usuario", 500);
        }

        // Creando el token de autenticación en texto plano
        $token = $user->createToken('auth_token')->plainTextToken;

        // Agrega el usuario y el token a un arreglo
        $data = [$user, $token];

        // Devuelve el arreglo de datos Http 201 Creado
        return response()->json($data, 201);;
    }

    public function login(Request $request){
        // Comprueba la validación de los datos del usuario
        $validator = Validator::make($request->all(), [
            "email" => "required|email|exists:users",
            "password" => "required"
        ]);

        // Si la validación falla
        if($validator->fails()){
            // Devuelve mensaje + Http 422 - No cumple con el formato establecido
            return response()->json("Ha fallado la validación de los datos", 422);
        }

        $user = User::where('email', $request->email)->first();

        // Si las credenciales no son válidas...
        if(!$user || !Hash::check($request->password, $user->password)){
            // Devuelve el mensaje + http 401 No autorizado
            return response()->json("Las credenciales no son válidas", 401);
        }

        // Si todo esta bien crea el token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Al final devuelve el usuario y el token + http 200 OK
        return response()->json([$user, $token], 200);
    }

    public function logout(Request $request){
        // Para el logout debemos eliminar el token de sesión
        $request->user()->tokens()->delete();

        // Devuelve el mensaje y Http 200 Ok
        return response()->json("Has cerrado la sesión", 200);
    }
}
