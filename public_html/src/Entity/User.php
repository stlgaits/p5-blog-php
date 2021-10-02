<?php

namespace App\Entity;

class User
{
    private $id;
    private $username;
    private $email;
    private $first_name;
    private $last_name;
    private $password;
    private $roles;

    public function __construct(array $data=[]){
        $this->hydrate($data);
    }

    public function __get($property){
        if(property_exists($this, $property)){
            return $this->$property;
        }
    }

	public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        if(is_string($id) && intval($id) > 0){
            $this->id = intval($id);
        }
        if(is_int($id) && $id > 0) {
            $this->id = $id;
        }
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }


    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    
    public function getFirst_name()
    {
        return $this->first_name;
    }

    public function setFirst_name($first_name)
    {
        $this->first_name = $first_name;
    } 
    
    public function getLast_name()
    {
        return $this->last_name;
    }

    public function setLast_name($last_name)
    {
        $this->last_name = $last_name;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        $roles = json_decode($roles, true);
        // guarantees every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        $roles = array_unique($roles);
        return  json_encode($roles);
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
    
    private function hydrate($data){
        // Boucle sur tous les champs et valeurs
        foreach($data as $key => $value){
            // Construit le nom de la méthode grâce 
            // au nom des champs de la DB
            $methodName = 'set'.ucfirst($key);
            
            // Si la méthode existe
            if(method_exists($this, $methodName)){
                // Appel de la méthode
                $this->$methodName($value);
            }
        }
    }

    public function __toString()
    {
        return $this->username;
    }
}
