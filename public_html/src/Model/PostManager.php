<?php

namespace App\Model;

use PDO;
use DateTime;
use Exception;
use App\Entity\Post;

class PostManager extends Manager
{
    
    /**
     * Get all blogposts sorted by creation date
     *
     * @return array
     */
    public function readAll()
    {
        $posts = [];
        $sql = "SELECT * FROM post ORDER BY created_at DESC";
        // TODO: jointure sur le User pour éviter d'avoir à appeler un READ supplémentaire pour chaque userID
        $results = $this->db->query($sql);
        while ($post = $results->fetch()) {
            $posts[] = new Post($post);
        }
        return $posts;
    }

    /**
     * Find a blogpost by ID
     *
     * @param integer $id
     * @return Post
     */
    public function read(int $id)
    {
        $sql = "SELECT * FROM post WHERE id = ?";
        $r = $this->db->prepare($sql);
        $r->bindValue(1, $id, PDO::PARAM_INT);
        $r->execute();
        return new Post($r->fetch());
    }

    /**
     * Insert a new blogpost in database
     *
     * @param string $title
     * @param string $content
     * @param int $created_by
     * @param string $slug
     * @param string $leadSentence
     * @return int
     */
    public function create(string $title, string $content, int $created_by, string $slug, string $leadSentence)
    {
        $now = new DateTime();
        $sql = "INSERT INTO post(title, lead_sentence, content, created_at, created_by, slug) 
                VALUES(:title, :lead_sentence, :content, :created_at, :created_by, :slug)";
        $r = $this->db->prepare($sql);
        $r->execute(
            array(
            ':title' => $title,
            ':lead_sentence' => $leadSentence,
            ':content' => $content,
            ':created_at' => $now->format('Y-m-d H:i:s'),
            ':created_by' => $created_by,
            ':slug' => $slug
            )
        );
        $newPostId = $this->db->lastInsertId();

        return $newPostId;
    }

    /**
     * Delete a Blogpost from database
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $sql = "DELETE FROM post WHERE id=?";
        $r = $this->db->prepare($sql);
        $r->execute(array($id));
    }

    /**
     * Update a blog post
     *
     * @param integer $id
     * @param string $title
     * @param string $content
     * @param string $slug
     * @param string $leadSentence
     * @return void
     */
    public function update(int $id, string $title, string $content, string $slug, string $leadSentence)
    {
            $sql = "UPDATE post SET title = :title, lead_sentence = :lead_sentence, content = :content, slug = :slug, updated_at = :updated_at WHERE id = :id";
            $r = $this->db->prepare($sql);
            $now = new DateTime();
            $r->bindValue(':title', $title, PDO::PARAM_STR);
            $r->bindValue(':lead_sentence', $leadSentence, PDO::PARAM_STR);
            $r->bindValue(':content', $content, PDO::PARAM_STR);
            $r->bindValue(':slug', $slug, PDO::PARAM_STR);
            $r->bindValue(':updated_at', $now->format('Y-m-d H:i:s'));
            $r->bindValue(':id', $id, PDO::PARAM_INT);
            $r->execute();
    }
}
