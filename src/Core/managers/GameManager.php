<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 9/14/2017
 * Time: 5:51 PM
 */

namespace Core\managers;


use Core\player\PlayerClass;

abstract class GameManager{

    const WAITING = 0;
    const PVP = 1;

    const KOHI = "Kohi1v1";
    const GAPPLE = "Gapple1v1";
    const IRON = "IronSoup1v1";
    const BUHC = "BUHC1v1";

    /**
     * @param PlayerClass $player
     */
    abstract public function removePlayer(PlayerClass $player);

    /**
     * @return bool
     */
    abstract public function isJoinable(): bool;

    /**
     * @param $bool
     */
    abstract public function setJoinable($bool);

    /**
     * @param PlayerClass $player
     */
    abstract public function addPlayer(PlayerClass $player);

    /**
     * @return int|string
     */
    abstract public function getStatus(): int|string;

    /**
     * @param $status
     */
    abstract public function setStatus($status);

    /**
     * @param int $time
     */
    abstract public function setTime(int $time);

    /**
     * @return int
     */
    abstract public function getTime(): int;

    /**
     * @return array
     */
    abstract public function getPlayers(): array;

    abstract public function end();


    /**
     * @param PlayerClass $player
     */
    abstract public function win(PlayerClass $player);

    /**
     * @return string
     */
    abstract public function getGameType(): string;

    /**
     * @return string
     */
    abstract public function getName(): string;
}