<?php

namespace App\Model;

use PDO;
use Exception;
use App\Entity\User;

class UserManager extends Manager
{
    
    public function readAll()
    {
        $users = [];
        $sql = "SELECT * FROM user";
        $results = $this->db->query($sql);
        while ($user = $results->fetch()) {
            $users[] = new User($user);
        }
        return $users;
    }

    public function read($id)
    {
        $sql = "SELECT * FROM user WHERE id = ?";
        $r = $this->db->prepare($sql);
        $r->bindValue(1, $id, PDO::PARAM_INT);
        $r->execute();

        return new User($r->fetch());
    }

    public function findByEmail(string $email)
    {
        $sql = "SELECT * FROM user WHERE email = ?";
        $r = $this->db->prepare($sql);
        $r->bindValue(1, $email, PDO::PARAM_STR);
        $r->execute();
        $result = $r->fetch();
        if (!$result) {
            return null;
        }
        return new User($result);
    }

    public function create($username, $email, $first_name, $last_name, $password, $roles)
    {
        $sql = "INSERT INTO user(username, email, first_name, last_name, password, roles) 
                VALUES(:username, :email, :first_name, :last_name, :password, :roles)";
        $r = $this->db->prepare($sql);
        $r->execute(array(
            ':username' => $username,
            ':email' => $email,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':password' => $password,
            ':roles' => $roles
        ));
        $newUserId = $this->db->lastInsertId();
        return $newUserId;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM user WHERE id=?";
        $r = $this->db->prepare($sql);
        $r->execute(array($id));
    }

    public function update($id, $username, $email, $first_name, $last_name, $password, $roles)
    {
        $sql = "UPDATE user SET username = :username,
                                email = :email,
                                first_name = :first_name,
                                last_name = :last_name,
                                password = :password,
                                roles = :roles,
                WHERE id = :id";
        $r = $this->db->prepare($sql);
        $r->bindValue('username', $username, PDO::PARAM_STR);
        $r->bindValue('email', $email, PDO::PARAM_STR);
        $r->bindValue('first_name', $first_name, PDO::PARAM_STR);
        $r->bindValue('last_name', $last_name, PDO::PARAM_STR);
        $r->bindValue('password', $password, PDO::PARAM_STR);
        $r->bindValue('roles', $roles, PDO::PARAM_STR);
        $r->bindValue('id', $id, PDO::PARAM_INT);
        $r->execute();

        return new User($r->fetch());
    }

    public function getAllRoles()
    {
        $sql = "SELECT DISTINCT roles FROM user";
        $results = $this->db->query($sql);
        $results = $results->fetchAll();
        foreach ($results as $rolesArray) {
            foreach ($rolesArray as $rolelist) {
                $rolelist = json_decode($rolelist, true);
                $roles[] = $rolelist;
            }
        }
        return $this->sortRolesArray($roles);
    }

    public function sortRolesArray($roles)
    {
        $sortedRoles = [];
        foreach ($roles as $rolelist) {
            foreach ($rolelist as $role) {
                if (!in_array($role, $sortedRoles)) {
                    $sortedRoles[] = $role;
                }
            }
        }
        return $sortedRoles;
    }
}
