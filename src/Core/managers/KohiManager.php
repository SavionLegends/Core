<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 4/27/2017
 * Time: 4:57 PM
 */

namespace Core\managers;


use Core\Main;
use Core\player\PlayerClass;
use Core\utils\Prefix;
use Core\utils\Utils;
use JetBrains\PhpStorm\Pure;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;

class KohiManager extends GameManager{

    private $plugin, $server, $name, $status, $time, $players, $joinable;


    /**
     * KohiManager constructor.
     * @param Main $plugin
     * @param $name
     */
    #[Pure] public function __construct(Main $plugin, $name){
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->players = [];
        $this->name = $name;
        $this->status = GameManager::WAITING;
        $this->time = 1500;
        $this->joinable = true;
    }

    /**
     * @return bool
     */
    public function isJoinable(): bool{
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
    public function getPlayers(): array{
        return $this->players;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * @return string
     */
    public function getGameType(): string{
        return "Kohi1v1";
    }

    /**
     * @return Main
     */
    public function getPlugin(): Main{
        return $this->plugin;
    }

    /**
     * @return Server
     */
    public function getServer(): Server{
        return $this->server;
    }

    /**
     * @return int
     */
    public function getTime(): int{
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime(int $time): void{
        $this->time = $time;
    }

    /**
     * @return int|string
     */
    public function getStatus(): int|string{
        return $this->status;
    }

    /**
     * @param $status
     */
    public function setStatus($status): void{
        $this->status = $status;
    }

    /**
     * @param PlayerClass $player
     */
    public function addPlayer(PlayerClass $player): void{
        $this->players[$player->getRealName()] = $player->getRealName();
        $player->setInMatch($this);
        $player->sendMessage(Prefix::DEFAULT."You joined match ".$this->getName()."!");
    }

    /**
     * @param PlayerClass $player
     */
    public function removePlayer(PlayerClass $player): void{
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
       // $this->getPlugin()->getScheduler()->cancelTask(Main::$tasks[$this->getName()]);
        if(isset(Main::$tasks[$this->getName()])){
            $task = Main::$tasks[$this->getName()];
            if($task instanceof Task){
                $task->getHandler()->cancel();
            }
            unset(Main::$tasks[$this->getName()]);
        }
        if(count($this->getPlayers()) !== 0){
            foreach($this->getPlayers() as $name){
                $p = $this->getServer()->getPlayerExact($name);
                if($p instanceof PlayerClass){
                    Utils::sendLobbyItems($p);
                }
            }
        }
        $this->setStatus(GameManager::WAITING);
        $this->time = 1500;
        $this->players = [];
    }

    public function start(): void{
        $this->setStatus(GameManager::PVP);
        $this->time = 1500;
        foreach ($this->getPlayers() as $name) {
            $player = $this->getServer()->getPlayerExact($name);
            $player->getArmorInventory()->setHelmet(new Item(new ItemIdentifier(ItemTypeIds::DIAMOND_HELMET)));
            $player->getArmorInventory()->setChestplate(new Item(new ItemIdentifier(ItemTypeIds::DIAMOND_CHESTPLATE)));
            $player->getArmorInventory()->setLeggings(new Item(new ItemIdentifier(ItemTypeIds::DIAMOND_LEGGINGS)));
            $player->getArmorInventory()->setBoots(new Item(new ItemIdentifier(ItemTypeIds::DIAMOND_BOOTS)));

            $dia_sword = new Item(new ItemIdentifier(ItemTypeIds::DIAMOND_SWORD));
            $dia_sword->setCount(1);

            //todo: custom potion stack class

            $potion_1 = VanillaItems::SPLASH_POTION()->setType(PotionType::HARMING());
            $potion_1->setCount(4);

            $potion_2 = VanillaItems::SPLASH_POTION()->setType(PotionType::HEALING());
            $potion_2->setCount(8);


            $player->getInventory()->addItem($dia_sword);
            $player->getInventory()->addItem($potion_1);
            $player->getInventory()->addItem($potion_2);
            $player->sendMessage(Prefix::DEFAULT."Match has started!");
        }
    }

    public function win(PlayerClass $player): void{
        //stats update here
        $this->end();
    }


}