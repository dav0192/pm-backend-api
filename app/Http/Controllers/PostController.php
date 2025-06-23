<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;


// Implementamos la interfaz de HasMiddleware
class PostController extends Controller implements HasMiddleware
{
    // Agregamos la funcion de middleware
    public static function middleware(){
        // Agregamos el middleware auth:sanctum y excluimos las funciones index y show
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtiene todos los posts
        $posts = Post::all();

        // Si no hay ningún post registrado devuelve Http 404 - No encontrado
        if($posts->isEmpty()){
            return response()->json("No hay posts registrados", 404);
        }

        // Consulta exitosa de elementos Http 200 Ok
        return response()->json($posts, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Inicia el validador de datos
        $validator = Validator::make($request->all(), [
            "title" => "required|max:255",
            "body" => "required"
        ]);

        // Si la validación falla devuelve Http 422 - Formato Incorrecto
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        // Crea el objeto
        // Indicar que es necesario un usuario autenticado para crear un post
        // Es posible que no se cree la columna user_id del usuario que hizo el post
        // solo si usas SQLite3, usa un motor como mariadb o MySQL
        $post = $request->user()->posts()->create([
            "title" => $request->title,
            "body" => $request->body
        ]);

        // Si el objeto Post no pudo crearse devuelve Http 500 error interno del servidor
        if(!$post){
            return response()->json("No se pudo crear el post", 500);
        }

        // Devuelve mensaje de confimración + Http 201 - Creado
        return response()->json("Se ha registrado el post", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        // Busca el post en la bd
        $postM = Post::find($post->id);

        // Si no se encuentra el post
        if(!$postM){
            // Devuelve error Http 404 - No encontrado
            return response()->json("No se ha encontrado este post", 404);
        }

        // Devuelve el $post + Http 200 Ok
        return response()->json($postM, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('modify', $post);
        // Busca el post en la BD
        $postM = Post::find($post->id);

        // Si no se ha encontrado el post
        if(!$postM){
            // Devuelve Http 404 - No encontrado
            return response()->json("No se ha encontrado el post", 404);
        }

        // Valida los datos ingresados...
        $validator = Validator::make($request->all(), [
            "title" => "required|max:255",
            "body" => "required"
        ]);

        // Si la validación falla
        if($validator->fails()){
            // Crea arreglo con:
            $data = ["Los datos no cumplen con el formato establecido", $validator->errors()];
            // Devuelve los datos más Http 422 - No cumple con el formato de datos
            return response()->json($data, 422);
        }

        // Asigna los nuevos valores al objeto
        $postM->title = $request->title;
        $postM->body = $request->body;

        // Modifica los cambios en el objeto
        $postM->save();

        // Devuelve mensaje de confirmación Http 200 Ok
        return response()->json("Se han modificado los datos del post", 200);
    }

    public function partialUpdate(Request $request, Post $post){
        // Buscar el post por id
        $postM = Post::find($post->id);

        // Si no existe el post
        if(!$postM){
            // Devuelve mensaje Http 404 - No encontrado
            return response()->json("No se ha encontrado el post", 404);
        }

        // Crea el validador
        $validator = Validator::make($request->all(), [
            "title" => "required|max:255",
            "body" => "required"
        ]);

        // Si falla la validación
        if($validator->fails()){
            // Devuelve mensaje + errores + Http 422 - No cumple con el formato establecido
            $data = ["Los datos no cumplen con el formato establecido", $validator->errors()];
            return response()->json($data, 422);
        }

        // Si existe el atributo title en el request
        if($request->has('title')){
            // Asigna la variable
            $postM->title = $request->title;
        }

        // Si existe el atributo body en el request
        if($request->has('body')){
            // Asigna la variable
            $postM->body = $request->body;
        }

        // Guarda la información
        $postM->save();

        // Devuelve mensaje de confirmación Http 200 Ok
        return response()->json("Se han modificado los datos del post", 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // Busca el post por el id
        $postM = Post::find($post->id);

        // Si no existe el post
        if(!$postM){
            // Devuelve el mensaje Http 404 - No encontrado
            return response()->json("No existe este post", 404);
        }

        // Elimina el post
        $postM->delete();

        // Devuelve mensaje Http 200 Ok
        return response()->json("Se ha eliminado el post", 200);
    }
}
