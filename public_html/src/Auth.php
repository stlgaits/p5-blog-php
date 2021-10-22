<?php

namespace App;

use App\Entity\User;

interface Auth
{

    /**
     *
     * @return User|null
     */
    public function getUser(): ?User;
}
