<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 5/15/2017
 * Time: 5:01 PM
 */

namespace Core\commands\game;


use Core\commands\CoreCommand;
use Core\Main;
use Core\managers\GameManager;
use Core\player\PlayerClass;
use Core\utils\Permissions;
use Core\utils\Prefix;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class IronSoupCommand extends CoreCommand{

    private $plugin, $server;

    public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
        parent::__construct($plugin, $name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->setPermission(Permissions::IRON_SOUP_COMMAND);
    }
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
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if($args[0] === "join"){
            if($sender instanceof PlayerClass && $sender->isQueued() && $sender->gameType !== null){
                $sender->sendMessage(Prefix::DEFAULT."You are already queueing for ".$sender->gameType);
                return false;
            }
            if($sender instanceof PlayerClass){
                $sender->sendMessage(Prefix::DEFAULT."You are queued and looking for a match....");
                $sender->setQueued(true, false, "IronSoup1v1");
                $this->getPlugin()->findIronSoupMatch($sender);
            }else{
                $sender->sendMessage(Prefix::DEFAULT."Your player data isn't compatible with the server please rejoin!");
            }
        }
        if($args[0] === "set" && $sender->hasPermission(Permissions::ALL_PERMS)){
            $this->getPlugin()->isSetting[$sender->getName()] = [];
            $this->getPlugin()->isSetting[$sender->getName()]["type"] = "IronSoup1v1";
            $this->getPlugin()->isSetting[$sender->getName()]["int"] = 0;
            $sender->sendMessage(Prefix::DEFAULT."Please tap the first position for IronSoup1v1!");
        }
        return true;
    }
}