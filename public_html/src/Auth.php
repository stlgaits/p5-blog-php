<?php

namespace App;

use App\Session;
use App\Entity\User;
use App\Model\UserManager;

class Auth
{
    /**
     * User manager : PDO connection to Users stored in the database
     *
     * @var UserManager
     */
    private $userManager;

    /**
     * User session
     *
     * @var Session
     */
    private $session;

    
    public function __construct()
    {
        $this->session = new Session();
        $this->userManager = new UserManager();
    }

    /**
     * Checks whether user is currently logged in
     *
     * @return boolean
     */
    public function isLoggedIn(): bool
    {
        // User credentials are stored in User Session
        if (empty($this->session->get('userID')) && empty($this->session->get('username'))) {
            return false;
        }
        return true;
    }
    
    /**
     * Checks a user's role
     *
     * @return boolean
     */
    public function isAdmin($user): bool
    {
        if ($user->getRole() === false || $user->getRole() === 0 || $user->getROle() === '0') {
            return false;
        }
        return true;
    }

    /**
     * Checks whether the current user has admin role
     *
     * @return boolean
     */
    public function isCurrentUserAdmin(): bool
    {
        /**
         * First retrieve user from session
         */
        if ($this->isLoggedIn()) {
            $user = $this->userManager->read($this->session->get('userID'));
            $role = $user->getRole();
            if ($role === false || $role === 0 || $role === '0') {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Retrieves current user from session storage
    *
     * @return User
     */
    public function getCurrentUser(): User
    {
        return $this->userManager->read($this->session->get('userID'));
    }

    /**
     * Determines whether a user's account is disabled ("deleted") or not
     *
     * @param User $user
     * @return boolean
     */
    public function isDisabled($user): bool
    {
        if ($user->getDeleted() === true || $user->getDeleted() === 1 ||  $user->getDeleted() === '1') {
            return true;
        }
        return false;
    }
}
