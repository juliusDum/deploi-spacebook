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
class PlanetManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'planet';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function selectUserPlanet(): array
    {
        return $this->pdo->query(" SELECT id, planet_name FROM " . self::TABLE)->fetchAll();
    }
}
