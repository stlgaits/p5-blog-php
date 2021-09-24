<?php

namespace App\Model;

use PDO;
use App\Entity\Post;

class PostManager  extends Manager
{
    
    public function readAll(){
        $posts = [];
        $sql = "SELECT * FROM post";
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
        return New Post($r->fetch());
    }

}
