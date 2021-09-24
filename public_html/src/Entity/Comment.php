<?php

namespace App\Entity;

use DateTime;

class Comment
{
    private $id;
    private $title;
    private $content;
    private $createdAt;
    private $createdBy;
    private $postId;

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

	public function getTitle()
    {
        return $this->title;
    }
	

    public function setTitle($title){
        if(mb_strlen($title) <= 255){
            $this->title = $title;
        }else{
            $this->title = substr($title, 0, 255);
        }
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content){
        $this->content = strip_tags($content, ['p','a','i']);   
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreateAt($createdAt)
    {
        $format = 'Y-m-d H:i:s';
        // Teste la validité de la date
        $d = DateTime::createFromFormat($format, $createdAt);
        if($createdAt == $d->format($format)){
            $this->createdAt = $d->format($format);
        }
        $dd = new DateTime();
        $this->createdAt = $dd->format($format);
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setCreatedBy($user)
    {
        $this->createdBy = $user->getId();
    }

    public function getPostId()
    {
        return $this->postId;
    }

    public function setPostId($postId)
    {
        $this->postId = $postId;
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
