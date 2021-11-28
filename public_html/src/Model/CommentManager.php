<?php

namespace App\Model;

use PDO;
use App\Entity\Comment;

class CommentManager extends Manager
{
    /**
     * Get all comments 
     *
     * @return array of Comment objects
     */
    public function readAll()
    {
        $comments = [];
        $sql = "SELECT * FROM comment";
        $query = $this->db->prepare($sql);
        $query->execute();
        while ($comment = $query->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = new Comment($comment);
        }
        return $comments;
    }

    /**
     * Find 1 comment by ID
     *
     * @param integer $id
     * @return Comment
     */    
    public function read(int $id)
    {
        $sql = "SELECT * FROM comment WHERE id = ?";
        $r = $this->db->prepare($sql);
        $r->bindValue(1, $id, PDO::PARAM_INT);
        $r->execute();
        return new Comment($r->fetch());
    }


    /**
     * Allows to display Comment's author & Blogpost's title without instantiating objects
     *
     * @return array
     */
    public function readAllWithAuthorsAndPostTitle()
    {
        $comments = [];
        $sql = "SELECT comment.content, comment.title , comment.created_at, comment.post_id, comment.created_by, comment.status, comment.id, user.username, post.title AS post_title
                FROM comment 
                INNER JOIN user ON comment.created_by = user.id
                INNER JOIN post ON comment.post_id = post.id";
        $query = $this->db->prepare($sql);
        $query->execute();
        while ($comment = $query->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = $comment;
        }
        return $comments;
    }


    /**
     * Only display comments pending approval by admin
     *
     * @return array
     */
    public function readAllPendingComments()
    {
        $comments = [];
        $sql = "SELECT * FROM comment WHERE comment.status = PENDING";
        $query = $this->db->prepare($sql);
        $query->execute();
        while ($comment = $query->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = new Comment($comment);
        }
        return $comments;
    }

    /**
     * Update a comment's status
     *
     * @return void
     */
    public function updateCommentStatus(int $id, $status)
    {
        $sql = "UPDATE comment SET comment.status = :status WHERE id = :id";
        $r = $this->db->prepare($sql);
        $r->bindValue(':id', $id, PDO::PARAM_INT);
        $r->bindValue(':status', $status, PDO::PARAM_STR);
        $r->execute();
    }
}