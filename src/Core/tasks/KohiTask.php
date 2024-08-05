<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 5/18/2017
 * Time: 8:48 AM
 */

namespace Core\tasks;


use Core\Main;
use Core\managers\GameManager;
use Core\managers\KohiManager;
use Core\player\PlayerClass;
use Core\utils\Prefix;
use Core\utils\Utils;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class KohiTask extends Task {

    private GameManager $match;
    private Server $server;
    private Main $plugin;

    public function __construct(Main $plugin, GameManager $match, int $matchRunningNumber){
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->match = $match;
        $this->plugin->getLogger()->critical("Task starting for ".$this->match->getGameType()." match #".$this->match->getName());
    }

    /**
     * @return Main
     */
    public function getPlugin() : Main{
        return $this->plugin;
    }

    /**
     * @return Server
     */
    public function getServer(): Server{
        return $this->server;
    }

    /**
     * @return KohiManager
     */
    public function getMatch() : GameManager{
        return $this->match;
    }

    public function onRun(): void{
        $match = $this->getMatch();
        if(!isset(Main::$tasks[$match->getName()])) return;
        if($match->getStatus() === GameManager::WAITING){
            if(count($match->getPlayers()) === 0) $match->end();
            if(count($match->getPlayers()) !== 2){
                foreach($match->getPlayers() as $name){
                    $player = $this->getServer()->getPlayerExact($name);
                    if($player instanceof PlayerClass){
                        $player->sendTip(TextFormat::AQUA."               Waiting...".TextFormat::RED."\n     Need more players...[".count($match->getPlayers())."/2]");
                    }
                }
            }else if(count($match->getPlayers()) === 2){
                $match->start();
            }
        }
        if($match->getStatus() === GameManager::PVP){
          //  if(count($match->getPlayers()) === 0) $match->end();//testing usage
            $match->setTime(($match->getTime()-1));
            foreach($match->getPlayers() as $name){
                $player = $this->getServer()->getPlayerExact($name);
                $player->sendTip(TextFormat::AQUA."Match time: ".TextFormat::WHITE.gmdate("i:s",$match->getTime()));
            }
        }
    }
}