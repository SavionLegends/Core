<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 4/27/2017
 * Time: 4:59 PM
 */

namespace Core\managers;

use Core\Main;
use Core\player\PlayerClass;
use Core\utils\Prefix;
use Core\utils\Utils;

class IronSoupManager extends GameManager {


    private $plugin, $server, $name, $status, $time, $players, $joinable;


    /**
     * IronSoupManager constructor.
     * @param Main $plugin
     * @param $name
     */
    public function __construct(Main $plugin, $name){
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->players = [];
        $this->name = $name;
        $this->status = GameManager::WAITING;
        $this->time = 1500;
        $this->joinable = true;
    }

    /**
     * @return bool|mixed
     */
    public function isJoinable(){
        return $this->joinable ?? true;
    }

    /**
     * @param $bool
     */
    public function setJoinable($bool){
        $this->joinable = $bool;
    }

    /**
     * @return array
     */
    public function getPlayers(){
        return $this->players;
    }

    /**
     * @return mixed
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @return string
     */
    public function getGameType(){
        return "IronSoup1v1";
    }

    /**
     * @return Main
     */
    public function getPlugin(){
        return $this->plugin;
    }

    /**
     * @return \pocketmine\Server
     */
    public function getServer(){
        return $this->server;
    }

    /**
     * @return int|mixed
     */
    public function getTime(){
        return $this->time;
    }

    /**
     * @param int $time
     * @return mixed|void
     */
    public function setTime(int $time){
        $this->time = $time;
    }

    /**
     * @return int|mixed
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * @param $status
     * @return mixed|void
     */
    public function setStatus($status){
        $this->status = $status;
    }

    /**
     * @param PlayerClass $player
     * @return mixed|void
     */
    public function addPlayer(PlayerClass $player){
        $this->players[$player->getRealName()] = $player->getRealName();
        $player->setInMatch($this);
        $player->sendMessage(Prefix::DEFAULT."You joined match ".$this->getName()."!");
    }

    /**
     * @param PlayerClass $player
     * @return mixed|void
     */
    public function removePlayer(PlayerClass $player){
        unset($this->players[$player->getRealName()]);
        $player->setQueued(false, false , null);
        $player->removeFromMatch();
        if($player->isOnline()){
            $player->sendMessage(Prefix::DEFAULT."You left match ".$this->getName()."!");
            Utils::sendLobbyItems($player);
        }
    }

    /**
     * @return mixed|void
     */
    public function end(){
        $this->getPlugin()->getLogger()->info("Cancelling match task for ".$this->getGameType()." match #".$this->getName());
        $this->getPlugin()->getScheduler()->cancelTask(Main::$tasks[$this->getName()]);
        unset(Main::$tasks[$this->getName()]);
        if(count($this->getPlayers()) !== 0){
            foreach($this->getPlayers() as $name){
                $p = $this->getServer()->getPlayer($name);
                if($p instanceof PlayerClass){
                    $level = $this->getServer()->getDefaultLevel()->getSpawnLocation();
                    $p->teleport($level);
                    $p->removeAllEffects();
                    $p->getInventory()->clearAll();
                    $p->getArmorInventory()->clearAll();
                    $p->setMaxHealth(20);
                    $p->setHealth(20);
                    $this->removePlayer($p);
                    Utils::sendLobbyItems($p);
                }
            }
        }
        $this->status = GameManager::WAITING;
        $this->time = 1500;
        $this->players = [];
    }

    /**
     * @return mixed|void
     */
    public function start(){
        $this->status = self::PVP;
        $this->time = 1500;
        foreach ($this->getPlayers() as $name) {
            $player = $this->getServer()->getPlayer($name);
            //give armor and etc!
            $player->sendMessage(Prefix::DEFAULT."Match has started!");
        }
    }

    /**
     * @return mixed|void
     */
    public function win(){
        //stats update here
        $this->end();
    }

}