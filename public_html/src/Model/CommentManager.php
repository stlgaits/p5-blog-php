<?php

namespace App\Model;

use PDO;
use DateTime;
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
     * Allows to display all Comment's author & Blogpost's title without instantiating objects
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
     * Get all approved comments for a single blog post with its author's username
     *
     * @param int $postID
     * @return void
     */
    public function getApprovedComments($postID)
    {
        $comments = [];
        $sql = "SELECT comment.content, comment.title , comment.created_at, comment.post_id, comment.created_by, comment.status, comment.id, user.username
                FROM comment 
                INNER JOIN user ON comment.created_by = user.id
                WHERE post_id = :post_id
                AND comment.status = 'APPROVED'";
        $query = $this->db->prepare($sql);
        $query->bindValue(':post_id', $postID, PDO::PARAM_INT);
        $query->execute();
        while ($comment = $query->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = $comment;
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

    /**
     * Insert a new comment (with pending status) in the database
     *
     * @param string $title
     * @param string $content
     * @param integer $created_by Foreign key
     * @param integer $post_id Foreign key
     * @return int
     */
    public function create(string $title, string $content, int $created_by, int $post_id)
    {
        $now = new DateTime();
        $sql = "INSERT INTO comment(title, content, created_at, created_by, post_id, status) 
                VALUES(:title, :content, :created_at, :created_by, :post_id, :status)";
        $r = $this->db->prepare($sql);
        $r->execute(
            array(
            ':title' => $title,
            ':content' => $content,
            ':created_at' => $now->format('Y-m-d H:i:s'),
            ':created_by' => $created_by,
            ':post_id' => $post_id,
            ':status' => 'PENDING'
            )
        );
        $newCommentId = $this->db->lastInsertId();

        return $newCommentId;
    }
}
