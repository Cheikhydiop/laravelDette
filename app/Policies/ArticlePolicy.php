<?php

namespace App\Policies;

use App\Models\Article;

use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
   
    public function viewAny(Article $user): Response
    {
        return $user->role_id === 3 
            ? Response::allow()
            : Response::deny('Interdit vous avez pas le droit y acceder!.');
    }

  
    public function create(Article $user): Response
    {
        return $user->role_id === 3
            ? Response::allow()
            : Response::deny('You do not have permission to create users.');
    }

   
    public function view(Article $user): Response
    {
        return $user->role_id === 3
            ? Response::allow()
            : Response::deny('You do not have permission to view this user.');
    }

}
