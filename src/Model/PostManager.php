<?php

/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

class PostManager extends AbstractManager
{
    const TABLE = 'post';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectUserPosts($userId): array
    {
        $statement = $this->pdo->prepare("SELECT post.id, post.title, post.content, post.img, 
            post.user_id, post.score, user.firstname as user_firstname, user.lastname as user_lastname, 
            user.pseudo as user_pseudo
            FROM " . self::TABLE .
            " JOIN user ON post.user_id = user.id 
            WHERE user_id = :user_id 
            ORDER BY id DESC");
        $statement->bindValue('user_id', $userId, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function updatePost(array $post): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE .
            " SET `title` = :title, `content` = :content, `img` = :img
            WHERE id = :id");
        $statement->bindValue('id', $post['id'], \PDO::PARAM_INT);
        $statement->bindValue('title', $post['title'], \PDO::PARAM_STR);
        $statement->bindValue('content', $post['content'], \PDO::PARAM_STR);
        $statement->bindValue('img', $post['img'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function insert(array $post): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            " (`title`, `content`, `img`, `user_id`, `score`) VALUES (:title, :content, :img, :user_id, :score)");
        $statement->bindValue('title', $post['title'], \PDO::PARAM_STR);
        $statement->bindValue('content', $post['content'], \PDO::PARAM_STR);
        $statement->bindValue('img', $post['img'], \PDO::PARAM_STR);
        $statement->bindValue('user_id', $post['user_id'], \PDO::PARAM_INT);
        $statement->bindValue('score', 0, \PDO::PARAM_INT);
        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function delete($id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    public function like($id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("UPDATE ".self::TABLE." SET score = score + 1 WHERE id =:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
