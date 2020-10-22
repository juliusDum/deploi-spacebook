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
class GalaxyManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'galaxy';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectUserGalaxy(): array
    {
        return $this->pdo->query(" SELECT id, galaxy_name FROM " . self::TABLE)->fetchAll();
    }
}
