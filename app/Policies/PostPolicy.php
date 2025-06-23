<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    // Se supone que busca por el id del post para ello pasaremos el objeto Post Completo
    public function modify(User $user, Post $post): Response
    {
        // Estableciendo politicas de modificación
        // Si el post pertenece a ese usuario permitir, sino denegar
        return $user->id === $post->user_id
        ? Response::allow()
        : Response::deny("Tú no publicaste este post");
    }
}
