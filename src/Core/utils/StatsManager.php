<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 5/18/2017
 * Time: 6:56 PM
 */

namespace Core\utils;

use Core\player\PlayerClass;

class StatsManager{

    private $player;

    public function __construct(PlayerClass $player){
        $this->player = $player;
    }

    public function getPlayer(){
        return $this->player;
    }

    public function returnStats(){
       return "No stats for ".$this->getPlayer()->getRealName();
    }

}