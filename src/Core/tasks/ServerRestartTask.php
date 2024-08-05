<?php
/**
 * Created by PhpStorm.
 * User: 20deavaults
 * Date: 9/20/18
 * Time: 9:59 AM
 */

namespace Core\tasks;


use Core\Main;
use Core\player\PlayerClass;
use Core\utils\Prefix;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class ServerRestartTask extends Task{

    private $plugin, $time;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        $this->time = 3600;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function getServer(){
        return $this->getPlugin()->getServer();
    }

    public function getTime(){
        return $this->time;
    }

    public function reduceTime(){
        $time = $this->getTime();
        $this->time = ($time - 1);
    }

    public function onRun(): void{
        $this->reduceTime();

        foreach($this->getServer()->getOnlinePlayers() as $name){
            $player =$this->getServer()->getPlayerExact($name);
            if($player instanceof PlayerClass){
                if($player->isQueued() === false){
                    $player->sendTip(TextFormat::AQUA."Server restarting in ".TextFormat::WHITE.gmdate("i:s", $this->getTime()));
                }
            }
        }

        if($this->getTime() === 60){
            $this->getServer()->broadcastMessage(Prefix::DEFAULT."Server restarting in 1 minute!");
        }

        if($this->getTime() === 0){
            sleep(1);
            $this->getServer()->forceShutdown();
        }
    }
}