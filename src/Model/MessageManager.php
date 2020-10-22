<?php


namespace App\Model;

class MessageManager extends AbstractManager
{


    const TABLE = 'chat';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insertMessage(array $message)
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE. "(pseudo, content, date) 
        VALUES (:pseudo, :content, :date)");
        $statement->bindValue('pseudo', $message['pseudo'], \PDO::PARAM_STR);
        $statement->bindValue('content', $message['content'], \PDO::PARAM_STR);
        $statement->bindValue('date', $message['date'], \PDO::PARAM_STR);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }
    public function showMessage(): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE. " ORDER BY date DESC LIMIT 0, 10");
        $statement->execute();
        return $statement->fetchAll();
    }
}
