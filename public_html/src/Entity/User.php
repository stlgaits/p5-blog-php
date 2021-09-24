<?php

namespace App\Entity;

class User
{
    private $id;
    private $username;
    private $email;
    private $firstName;
    private $lastName;
    private $password;
    private $roles = [];

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

    
    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    } 
    
    public function getLasttName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantees every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles)
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
}
