<?php

namespace App\Entity;

use DateTime;

class Post
{
    /**
     * @var int
     */
    private $id;
    private $title;
    private $content;
    private $createdAt;
    private $updatedAt;
    private $createdBy;
    private $slug;
    private $leadSentence;

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

    public function getId(): int
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

    public function getLead_Sentence()
    {
        return $this->leadSentence;
    }

    public function setLead_Sentence($leadSentence)
    {
        $this->leadSentence = strip_tags($leadSentence, ['p','a','i']);
    }

    public function getCreated_At()
    {
        return $this->createdAt;
    }

    public function setCreated_At($createdAt)
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

    public function getUpdated_At()
    {
        return $this->updatedAt;
    }

    public function setUpdated_At($updatedAt)
    {
        if (!empty($updatedAt)) {
            $format = 'Y-m-d H:i:s';
            // Teste la validité de la date
            $d = DateTime::createFromFormat($format, $updatedAt);

            if ($updatedAt == $d->format($format)) {
                $this->updatedAt = $d->format($format);
            } else {
                $dd = new DateTime();
                $this->updatedAt = $dd->format($format);
            }
        }
    }

    public function getCreated_By()
    {
        return $this->createdBy;
    }

    public function setCreated_By($userId)
    {
        $this->createdBy = $userId;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
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
