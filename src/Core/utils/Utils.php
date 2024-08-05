<?php
/**
 * Created by PhpStorm.
 * User: 20deavaults
 * Date: 10/4/18
 * Time: 10:28 AM
 */

namespace Core\utils;


use Core\Main;
use Core\managers\GameManager;
use Core\player\PlayerClass;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Utils{

    public static int $currentRunningKohi = 0;
    public static int $currentRunningIronSoup = 0;

    private $plugin, $db;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        $this->db = new \mysqli(!is_null($this->plugin->settings->get("server-name")) ? $this->plugin->settings->get("server-name") : "localhost", $this->plugin->settings->get("username"), $this->plugin->settings->get("password"), $this->plugin->settings->get("db_name"), !is_null($this->plugin->settings->get("port")) ? ((int)$this->plugin->settings->get("port")) : 3306);
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function getDB(){
        return $this->db;
    }

    /**
     * @param $levelName
     * Map that is being used and will be backed up/restored
     * @param GameManager $match
     */
    public static function resetMap($levelName, GameManager $match){
        if(dir(Main::getInstance()->getDataFolder()."World_Backups/$levelName") === null){
            $match->setJoinable(false);
            Main::getInstance()->getLogger()->critical("Map backup for match ".$match->getName()." for ".$match->getGameType()." is missing!");
            return;
        }
        /*Server::getInstance()->unloadLevel(Server::getInstance()->getLevelByName($levelName));
        $tmpMap = Server::getInstance()->getDataPath() . "worlds/$levelName/";
        $baseMap = (Main::getInstance()->getDataFolder()."/World_Backups/$levelName/");
        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($baseMap)) as $file){
            $rel = substr(realpath($file), strlen(realpath($baseMap)));
            copy($file, $tmpMap . $rel);
        }*/
        self::resetLevel($levelName);
    }


    /**
     * @param $backupPath
     * @param $worldPath
     */
    public static function doBackup($backupPath, $worldPath){
        $zip = new \ZipArchive;
        $zip->open($backupPath, \ZipArchive::CREATE);
        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($worldPath)) as $file){
            $zip->addFile($file, str_replace("\\", "/", ltrim(substr($file, strlen($worldPath)), "/\\")));
        }
    }

    /**
     * @param $src
     * @param $dst
     */
    public static function recurse_copy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !==($file = readdir($dir))){
            if(($file != '.') && ($file != '..' )){
                if(is_dir($src . '/' . $file)){
                    self::recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }else{
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * @param $levelName
     */
    public static function resetLevel($levelName){
        $server = Server::getInstance();
        $main = Main::getInstance();
        $worldPath = $server->getDataPath() . "worlds/".$levelName;
        self::file_delDir($worldPath);
        $main->getLogger()->info("DELETED WORLD REGION!");
        self::recurse_copy($main->getDataFolder()."World_Backups/".$levelName."/",$server->getDataPath()."worlds/".$levelName."/");
        $main->getLogger()->info("RESTORED WORLD!");
    }

    /**
     * @param $dir
     */
    public static function file_delDir($dir){
        $dir = rtrim($dir, "/\\") . "/";
        foreach(scandir($dir) as $file){
            if($file === "." or $file === ".."){
                continue;
            }
            $path = $dir . $file;
            if(is_dir($path)){
                self::file_delDir($path);
            }else{
                unlink($path);
            }
        }
        rmdir($dir);
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function generateRandomInt($length = 10) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function sendLobbyItems(PlayerClass $player){
        $inventory = $player->getInventory();

        $inventory->clearAll();
        $inventory->setHeldItemIndex(5, true);

        $level = Main::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
        $player->teleport($level);
        $player->getEffects()->clear();
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->setMaxHealth(20);
        $player->setHealth(20);

        $wooden_sword = new Item(new ItemIdentifier(ItemTypeIds::WOODEN_SWORD));
        $stone_sword = new Item(new ItemIdentifier(ItemTypeIds::STONE_SWORD));
        $golden_sword = new Item(new ItemIdentifier(ItemTypeIds::GOLDEN_SWORD));
        $iron_sword = new Item(new ItemIdentifier(ItemTypeIds::IRON_SWORD));
        $diamond_sword = new Item(new ItemIdentifier(ItemTypeIds::DIAMOND_SWORD));

        $inventory->setItem(0, $wooden_sword->setCustomName(TF::AQUA.TF::BOLD."Kohi1v1\n\n".TF::AQUA.TF::BOLD."~Tap sword on ground to join a match!~"), true);
        $inventory->setItem(1, $stone_sword->setCustomName(TF::AQUA.TF::BOLD."IronSoup1v1\n\n".TF::AQUA.TF::BOLD."~Tap sword on ground to join a match!~"), true);
        $inventory->setItem(2, $golden_sword->setCustomName(TF::AQUA.TF::BOLD."BUHC1v1\n\n".TF::AQUA.TF::BOLD."~Tap sword on ground to join a match!~"), true);
        $inventory->setItem(3, $iron_sword->setCustomName(TF::AQUA.TF::BOLD."Gapple1v1\n\n".TF::AQUA.TF::BOLD."~Tap sword on ground to join a match!~"), true);
        $inventory->setItem(4, $diamond_sword->setCustomName(TF::AQUA.TF::BOLD."SOON....."), true);

        if($player->isQueued() === true){
            $player->sendMessage(Prefix::DEFAULT."You have left the game/stopped queueing!");
            if($player->getMatch() !== null){
                $player->removeFromMatch();
            }
            $player->setQueued(false, false, null);
        }
    }

    public function getPasswordFromSql($playerName){
        $password = $this->getDB()->query("SELCET * FROM registered_players WHERE name = '".$this->getDB()->escape_string($playerName)."';");
        if($password){
            return $password;
        }else{
            return null;
        }
    }

}