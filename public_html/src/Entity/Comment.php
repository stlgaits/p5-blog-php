<?php

namespace App\Entity;

use DateTime;

class Comment
{
    private $id;
    private $title;
    private $status;
    private $content;
    private $createdAt;
    private $createdBy;
    private $postId;
    // TODO: if possible when a PHP 8.1 fpm alpine image is released on Docker hub, replace this with an Enum class
    const PENDING = 'PENDING';
    const REJECTED = 'REJECTED';
    const APPROVED = 'APPROVED';

    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        if (is_string($id) && intval($id) > 0) {
            $this->id = intval($id);
        }
        if (is_int($id) && $id > 0) {
            $this->id = $id;
        }
    }

    public function getTitle()
    {
        return $this->title;
    }
    

    public function setTitle($title)
    {
        if (mb_strlen($title) <= 255) {
            $this->title = $title;
        } else {
            $this->title = substr($title, 0, 255);
        }
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = strip_tags($content, ['p','a','i']);
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreated_at($createdAt)
    {
        $format = 'Y-m-d H:i:s';
        // Teste la validité de la date
        $d = DateTime::createFromFormat($format, $createdAt);
        if ($createdAt == $d->format($format)) {
            $this->createdAt = $d->format($format);
        } else {
            $dd = new DateTime();
            $this->createdAt = $dd->format($format);
        }
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setCreated_by($user)
    {
        $this->createdBy = $user;
    }

    public function getPostId()
    {
        return $this->postId;
    }

    public function setPost_id($postId)
    {
        $this->postId = $postId;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        /**
         * Pending status by default for new comments
         */
        $status = $status ?? $this::PENDING;
        $this->status = $status;
    }

    private function hydrate($data)
    {
        // Boucle sur tous les champs et valeurs
        foreach ($data as $key => $value) {
            // Construit le nom de la méthode grâce
            // au nom des champs de la DB
            $methodName = 'set' . ucfirst($key);
            // Si la méthode existe
            if (method_exists($this, $methodName)) {
                // Appel de la méthode
                $this->$methodName($value);
            }
        }
    }
}
