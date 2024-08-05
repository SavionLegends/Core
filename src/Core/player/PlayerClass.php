<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 4/27/2017
 * Time: 4:39 PM
 */

namespace Core\player;

use Core\Main;
use Core\managers\GameManager;
use Core\tasks\IronSoupTask;
use Core\tasks\KohiTask;
use Core\utils\Permissions;
use Core\utils\Prefix;
use Core\utils\StatsManager;
use Core\utils\TextToHead;
use Core\utils\Utils;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\PlayerInfo;
use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayerClass extends Player
{


    public $loginTrys = [];

    public $kohiMatch = [];

    private $queued = false;

    public $inGame;

    public $gameType;

    private $statsManager;

    private $match;

    private $plugin;

    private $session;

    public $matchType;

    private $loaderID;

    private $ids = [];


    /**
     * @param Server $server
     * @param NetworkSession $session
     * @param PlayerInfo $playerInfo
     * @param bool $authenticated
     * @param Location $spawnLocation
     * @param CompoundTag|null $namedtag
     */
    public function __construct(Server $server, NetworkSession $session, PlayerInfo $playerInfo, bool $authenticated, Location $spawnLocation, ?CompoundTag $namedtag){
        parent::__construct($server, $session, $playerInfo, $authenticated, $spawnLocation, $namedtag);
        $this->plugin = Main::getInstance();
        $this->server = Main::getInstance()->getServer();
        $this->session = $session;

        $database = Main::getInstance()->database->getAll();
        if(!$this->isRegistered()){
            $database[strtolower($playerInfo->getUsername())] = [];
            $database[strtolower($playerInfo->getUsername())]["uuid"] = $playerInfo->getUuid()->toString();
            $database[strtolower($playerInfo->getUsername())]["time"] = date("D, F d, Y, H:i T");
            $database[strtolower($playerInfo->getUsername())]["rank"] = "default";
            $database[strtolower($playerInfo->getUsername())]["uploaded_to_sql"] = false;
            $database[strtolower($playerInfo->getUsername())]["name"] = strtolower($playerInfo->getUsername());
            $database[$playerInfo->getUsername()]["lang"] = "en";
            $database[strtolower($playerInfo->getUsername())]["permissions"][Permissions::DEFAULT] = true;
            $database[strtolower($playerInfo->getUsername())]["permissions"][Server::BROADCAST_CHANNEL_USERS] = true;
            $this->getPlugin()->database->setAll($database);
            $this->getPlugin()->database->save();
            $this->getPlugin()->database->reload();
        }
    }

    /**
     * @return Main
     */
    public function getPlugin(): Main{
        return $this->plugin;
    }

    /**
     * @return NetworkSession
     */
    public function getNetworkSession(): NetworkSession{
        return $this->session;
    }

    /* TODO: implement permissions handling.
    public function hasPermission($permission): bool{
        $database = $this->getPlugin()->database->getAll();
        if($this->getPlugin()->database->exists($this->getName())){
            if(isset($database[$this->getName()]["permissions"][$permission])){
                $perm = $database[$this->getName()]["permissions"][$permission];
                if($perm === true){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    public function addPermission($permission){
        $database = $this->getPlugin()->database->getAll();
        $database[strtolower($this->getName())]["permissions"][$permission] = true;
        $this->getPlugin()->database->setAll($database);
        $this->getPlugin()->database->save();
        $this->getPlugin()->database->reload();
    }

    public function removePermission($permission){
        $database = $this->getPlugin()->database->getAll();
        $database[strtolower($this->getName())]["permissions"][$permission] = false;
        $this->getPlugin()->database->setAll($database);
        $this->getPlugin()->database->save();
        $this->getPlugin()->database->reload();
    }
*/

    /**
     * @return Server
     */
    public function getServer(): Server{
        return parent::getServer();
    }


    /**
     * @return string
     */
    public function getName(): string{
        return strtolower(parent::getName());
    }

    /** Returns player name in regular form
     * @return string
     */
    public function getRealName(): string{
        return parent::getName();
    }


    /**
     * @return mixed
     */
    public function getPassword(): mixed{
        $database = $this->getPlugin()->database->getAll();
        if($this->isRegistered()){
            return $database[strtolower($this->getName())]["password"];
        }else{
            return -1;
        }
    }

    /**
     * @return bool
     */
    public function isRegistered(): bool{
        if($this->getPlugin()->database->exists(strtolower($this->getName()))){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool{
        if(isset($this->getPlugin()->loggedIn[$this->getName()])){
            return true;
        }else{
            return false;
        }
    }

    public function logout(){
        unset($this->getPlugin()->loggedIn[$this->getName()]);
    }

    /**
     * @return GameManager|null
     * returns what match player is in
     */
    public function getMatch(): ?GameManager{
        if($this->match instanceof GameManager){
            return $this->match;
        }else{
            return null;
        }
    }

    /**
     * @return void
     */
    public function removeFromMatch(){
        if($this->isQueued()){
            $this->getMatch()->removePlayer($this);
        }
        $this->match = null;
        $this->getPlugin()->playersInMatches--;
    }

    /**
     * @return bool
     */
    public function isQueued(): bool{ // will be used for checking is player is in a match(any gamemode)
        return $this->queued;
    }


    /**
     * @param bool $bool
     * @param bool $inGame
     * @param $gameType
     */
    public function setQueued(bool $bool, bool $inGame, $gameType){
        $this->queued = $bool;
        if($bool === false){
            $this->inGame = false;
            $this->gameType = null;
        }else{
            $this->inGame = $inGame;
            $this->gameType = $gameType;
        }
    }

    /**
     * @return mixed
     */
    public function getLang(): mixed{
        $database = $this->getPlugin()->database->getAll();
        return $database[$this->getName()]["lang"];
    }

    /**
     * @param string $lang
     */
    public function setLang(string $lang){
        $database = $this->getPlugin()->database->getAll();
        $database[$this->getName()]["lang"] = $lang;
        $this->getPlugin()->database->setAll($database);
        $this->getPlugin()->database->save();
        $this->sendMessage(Prefix::DEFAULT."Set language to ".$lang);
    }

    /**
     * @param GameManager $match
     */
    public function setInMatch(GameManager $match){
        $this->match = $match;
    }

    /**
     * @return StatsManager
     */
    public function getStatsManager() : StatsManager{
        return $this->statsManager;
    }

    /**
     * @return string
     */
    public function getStats(): string{
        return $this->getStatsManager()->returnStats();
    }


    /*public function getKohiMatch(){
        return isset($this->kohiMatch["match"]) ? $this->kohiMatch["match"] : null;
    }*/


    /**
     * @param GameManager $match
     * @return bool
     */
    public function joinMatch(GameManager $match): bool{
        if($match->getStatus() === GameManager::PVP){
            return false;
        }

        if(count($match->getPlayers()) === 2){
            return false;
        }

        if(!$match->isJoinable()){
            $this->sendMessage(Prefix::DEFAULT."Match #".$match->getName()." for ".$match->getGameType()." isn't joinable please try again later!");
            return false;
        }
        
        if($this->isQueued()){
            return false;
        }

        if(count($match->getPlayers()) === 0){
            if($match->getGameType() === "Kohi1v1"){
                Utils::$currentRunningKohi = (Utils::$currentRunningKohi + 1);
                $task = new KohiTask($this->getPlugin(), $match, Utils::$currentRunningKohi);
                $h = $this->getPlugin()->getScheduler()->scheduleRepeatingTask($task, 20);
                $task->setHandler($h);
                Main::$tasks[$match->getName()] = $task;
            }

            //TODO: REST OF TASKS!
        }

        $match->addPlayer($this);
        echo var_dump($match->getPlayers());

        $cfg = $this->getPlugin()->matchesConfig->getAll();
        $pos1 = $cfg[$match->getGameType()."-matches"][$match->getName()]["Positions"]["pos1"];
        $pos2 = $cfg[$match->getGameType()."-matches"][$match->getName()]["Positions"]["pos2"];
        $level = $cfg[$match->getGameType()."-matches"][$match->getName()]["Positions"]["pos2"]["level"];

        if(!$this->getServer()->getWorldManager()->isWorldLoaded($level)){
            $this->getServer()->getWorldManager()->loadWorld($level);
        }

        if(count($match->getPlayers()) === 1){
            $this->teleport(new Position($pos1["x"], $pos1["y"]+1.5, $pos1["z"], $this->getServer()->getWorldManager()->getWorldByName($pos1["level"])));
        }else{
            $this->teleport(new Position($pos2["x"], $pos2["y"]+1.5, $pos2["z"], $this->getServer()->getWorldManager()->getWorldByName($pos2["level"])));
        }
        $this->getInventory()->clearAll();
        $this->getPlugin()->playersInMatches++;

        return true;
    }
}

