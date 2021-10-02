<?php

namespace App\Model;

use PDO;
use App\Entity\Post;
use DateTime;

class PostManager  extends Manager
{
    
    public function readAll(){
        $posts = [];
        $sql = "SELECT * FROM post ORDER BY created_at";
        $results = $this->db->query($sql);
        while($post = $results->fetch()){
            $posts[] = new Post($post);
        }
        return $posts;
    }

    public function read($id){
        $sql = "SELECT * FROM post WHERE id = ?";
        $r = $this->db->prepare($sql);
        $r->bindValue(1, $id, PDO::PARAM_INT);
        $r->execute();

        return new Post($r->fetch());
    }

    public function create($title, $content, $created_by, $slug){
        $now = new DateTime();
        $sql = "INSERT INTO post(title, content, created_at, created_by, slug) 
                VALUES(:title, :content, :created_at, :created_by, :slug)";
        $r = $this->db->prepare($sql);
        $r->execute(array(
            ':title' => $title,
            ':content' => $content,
            ':created_at' => $now->format('Y-m-d H:i:s'),
            ':created_by' => $created_by,
            ':slug' => $slug 
        ));
        $newPostId = $this->db->lastInsertId();
        return $newPostId;
    }

    public function delete($id){
        $sql = "DELETE FROM post WHERE id=?";
        $r = $this->db->prepare($sql);
        $r->execute(array($id));
    }

    public function update($id, $title, $content, $slug){
        $sql = "UPDATE post SET title = :title,
                                content = :content,
                                slug = :slug
                WHERE id = :id";
        $r = $this->db->prepare($sql);
        $r->bindValue('title', $title, PDO::PARAM_STR);
        $r->bindValue('content', $content, PDO::PARAM_STR);
        $r->bindValue('slug', $slug, PDO::PARAM_STR);
        $r->bindValue('id', $id, PDO::PARAM_INT);
        $r->execute();

        return new Post($r->fetch());
    }

}