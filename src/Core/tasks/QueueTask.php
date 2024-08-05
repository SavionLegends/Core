<?php
/**
 * Created by PhpStorm.
 * User: 20deavaults
 * Date: 9/11/18
 * Time: 9:10 AM
 */

namespace Core\tasks;


use Core\Main;

use Core\player\PlayerClass;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class QueueTask extends Task{

    private $plugin, $server;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function getServer(){
        return $this->plugin->getServer();
    }

    public function onRun(int $currentTick){
        foreach($this->getServer()->getOnlinePlayers() as $name){
            $player = $this->getServer()->getPlayer($name);
            if($player instanceof PlayerClass && $player->isQueued() && $player->inGame === false){
                $player->sendTip(TextFormat::AQUA."Queueing for ".$player->gameType);
            }
        }
    }

}