<?php
/**
 * Created by PhpStorm.
 * User: 20deavaults
 * Date: 8/31/18
 * Time: 10:47 AM
 */

namespace Core\tasks;

use Core\Main;
use Core\managers\GameManager;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class IronSoupTask extends Task{

    private $plugin, $match;

    public function __construct(Main $plugin, GameManager $match){
        $this->plugin = $plugin;
        $this->match = $match;
        $this->plugin->getLogger()->critical(Prefix::DEFAULT."Task starting for ".$this->match->getGameType()." match #".$this->match->getName());
    }

    /**
     * @return Main
     */
    public function getPlugin() : Main{
        return $this->plugin;
    }

    /**
     * @return GameManager
     */
    public function getMatch() : GameManager{
        return $this->match;
    }

    /**
     * @return Server
     */
    public function getServer() : Server{
        return $this->getPlugin()->getServer();
    }

    public function onRun(): void{

    }
}