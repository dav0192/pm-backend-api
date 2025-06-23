<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;


// TODO: Para agregar tiempo de expiración al token ir a Config\sanctum.php...

// Ruta protegida que devuelve la información del usuario si esta autenticado...
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Ruta de prueba
Route::get('/', function(){
    return "Hola, soy una API";
});

// Enlista todos los metodos del controlador de este modelo
// Route::apiResource('posts', PostController::class);

// Mostrar todos los Posts
Route::get('/posts', [PostController::class, 'index']);
// Consultar 1 elemento
Route::get('/posts/{post}', [PostController::class, 'show']);
// Guardar un post
Route::post('/posts', [PostController::class, 'store']);
// Actualizar un post
Route::put('/posts/{post}', [PostController::class, 'update']);
// Actualizar parcialmente un post
Route::patch('/posts/{post}', [PostController::class, 'partialUpdate']);
// Eliminar un post
Route::delete('/posts/{post}', [PostController::class, 'destroy']);

// Rutas de autenticación
Route::post('/registro', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Agregando middleware de protección de sanctum
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
