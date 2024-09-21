<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
      /**
     * Determine whether the user can view the document.
     */
    public function view(User $user, Document $document): Response
    {
        return $user->id === $document->user_id
                ? Response::allow()
                : Response::deny('You do not own this document.');
    }

    /**
     * Determine whether the user can update the document.
     */
    public function update(User $user, Document $document): Response
    {
        return $user->id === $document->user_id
                ? Response::allow()
                : Response::deny('You do not own this document.');
    }

    /**
     * Determine whether the user can delete the document.
     */
    public function delete(User $user, Document $document): Response
    {
        return $user->id === $document->user_id
                ? Response::allow()
                : Response::deny('You do not own this document.');
    }
}
