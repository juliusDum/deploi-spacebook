<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

/**
 *
 */
class FriendManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'friend';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectFriend($id): array
    {
        $statement = $this->pdo->prepare("SELECT friend_id FROM " . self::TABLE . " WHERE user_id = :id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function deleteFriends($id)
    {
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " 
        WHERE friend_id = :friend_id AND user_id = :user_id");
        $statement->bindValue('friend_id', $id, \PDO::PARAM_INT);
        $statement->bindValue('user_id', $_SESSION['id'], \PDO::PARAM_INT);

        $statement->execute();
    }

    public function addFriend($id)
    {
        $statement = $this->pdo->prepare(" INSERT INTO " .self::TABLE. " (user_id, friend_id, status_id)  
        SELECT :user_id, :friend_id, 2 FROM DUAL 
        WHERE NOT EXISTS (SELECT * FROM " .self::TABLE. "  WHERE user_id=:user_id AND friend_id=:friend_id LIMIT 1 )");
        $statement->bindValue('friend_id', $id, \PDO::PARAM_INT);
        $statement->bindValue('user_id', $_SESSION['id'], \PDO::PARAM_INT);
        $statement->execute();
    }
}
