<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 4/27/2017
 * Time: 5:00 PM
 */

namespace Core\managers;

use Core\Main;
use Core\player\PlayerClass;
use Core\utils\Prefix;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use Core\utils\Utils;

/**
 Possibility to make class file support use of BUHC FFA?
 */

class BUHCManager extends GameManager{

    private $plugin, $server, $name, $status, $time, $players, $joinable;


    /**
     * BUHCManager constructor.
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
     * @return null|\pocketmine\level\Level
     */
    private function getLevel(){
        $database = $this->getPlugin()->matchesConfig->getAll();
        $level = $database["BUHC1v1-matches"][$this->getName()]["pos1"]["level"];
        return $this->getServer()->getLevelByName($level);
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
        return "BUHC1v1";
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
        $player->setQueued(false, false, null);
        $player->removeFromMatch();
        if($player->isOnline()){
            $player->sendMessage(Prefix::DEFAULT."You left match ".$this->getName()."!");
            Utils::sendLobbyItems($player);
        }
    }

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
        Utils::resetMap($this->getLevel(), $this);
    }

    public function start(){
        $this->status = self::PVP;
        $this->time = 1500;
        foreach ($this->getPlayers() as $name) {
            $player = $this->getServer()->getPlayer($name);
            $player->getArmorInventory()->setHelmet(Item::get(Item::DIAMOND_HELMET));
            $player->getArmorInventory()->setChestplate(Item::get(Item::DIAMOND_CHESTPLATE));
            $player->getArmorInventory()->setLeggings(Item::get(Item::DIAMOND_LEGGINGS));
            $player->getArmorInventory()->setBoots(Item::get(Item::DIAMOND_BOOTS));

            $player->getInventory()->addItem(Item::get(ItemIds::DIAMOND_SWORD, 0, 1));
            $player->getInventory()->sendContents($player);
            $player->getInventory()->addItem(Item::get(ItemIds::DIAMOND_PICKAXE,0,1));
            $player->getInventory()->sendContents($player);
            $player->getInventory()->addItem(Item::get(ItemIds::COBBLESTONE,0,64));
            $player->getInventory()->sendContents($player);
            $player->getInventory()->addItem(Item::get(ItemIds::BOW, 0, 1));
            $player->getInventory()->sendContents($player);
            $player->getInventory()->addItem(Item::get(ItemIds::ARROW, 0, 64));
            $player->getInventory()->sendContents($player);
            $player->getInventory()->addItem(Item::get(ItemIds::GOLDEN_APPLE, 0, 32));
            $player->getInventory()->sendContents($player);
            $player->sendMessage(Prefix::DEFAULT."Match has started!");
        }
    }

    public function win(){
        //stats update here
        $this->end();
    }
}