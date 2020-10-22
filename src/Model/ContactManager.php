<?php


/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

class ContactManager extends AbstractManager
{

    const TABLE = 'contact_us';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }
    public function insert(array $contact): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (subject, message, email) 
        VALUES (:subject, :message, :email)");
        $statement->bindValue('subject', $contact['subject'], \PDO::PARAM_STR);
        $statement->bindValue('message', $contact['message'], \PDO::PARAM_STR);
        $statement->bindValue('email', $contact['email'], \PDO::PARAM_STR);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function deleteMessageById(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
