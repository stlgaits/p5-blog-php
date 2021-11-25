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

    public function create($username, $email, $first_name, $last_name, $password, $role)
    {
        $sql = "INSERT INTO user(username, email, first_name, last_name, password, role) 
                VALUES(:username, :email, :first_name, :last_name, :password, :role)";
        $r = $this->db->prepare($sql);
        $r->execute(
            array(
            ':username' => $username,
            ':email' => $email,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':password' => $password,
            ':role' => $role
            )
        );
        $newUserId = $this->db->lastInsertId();
        return $newUserId;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM user WHERE id=?";
        $r = $this->db->prepare($sql);
        $r->execute(array($id));
    }

    public function update($id, $username, $email, $first_name, $last_name, $password, $role)
    {
        $sql = "UPDATE user SET username = :username,
                                email = :email,
                                first_name = :first_name,
                                last_name = :last_name,
                                password = :password,
                                role = :role,
                WHERE id = :id";
        $r = $this->db->prepare($sql);
        $r->bindValue('username', $username, PDO::PARAM_STR);
        $r->bindValue('email', $email, PDO::PARAM_STR);
        $r->bindValue('first_name', $first_name, PDO::PARAM_STR);
        $r->bindValue('last_name', $last_name, PDO::PARAM_STR);
        $r->bindValue('password', $password, PDO::PARAM_STR);
        $r->bindValue('role', $role, PDO::PARAM_STR);
        $r->bindValue('id', $id, PDO::PARAM_INT);
        $r->execute();

        return new User($r->fetch());
    }
}
