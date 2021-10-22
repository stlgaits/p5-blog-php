<?php

namespace App;

use App\Entity\User;

class DatabaseAuth implements Auth
{

    /**
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return null;
    }
}
