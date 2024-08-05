<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 5/15/2017
 * Time: 5:47 PM
 */

namespace Core\commands\custom;

use Core\commands\CoreCommand;
use Core\Main;
use Core\player\PlayerClass;
use Core\utils\Permissions;
use Core\utils\Prefix;
use Core\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class QuitCommand extends CoreCommand{

    private $plugin, $server;

   public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
       parent::__construct($plugin, $name, $description, $usageMessage, $aliases);
       $this->plugin = $plugin;
       $this->server = $plugin->getServer();
       $this->setPermission(Permissions::QUIT_COMMAND);
   }

    public function getPlugin() : Main{
        return $this->plugin;
    }

    public function getServer(): Server{
        return $this->server;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if($sender instanceof PlayerClass){
            if($sender->isQueued()){
                $sender->sendMessage(Prefix::DEFAULT."You have left the game/stopped queueing!");
                if($sender->getMatch() !== null){
                    $sender->removeFromMatch();
                }
                $sender->setQueued(false, false, null);
                $level = $this->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
                $sender->teleport($level);
                $sender->getEffects()->clear();
                $sender->getInventory()->clearAll();
                $sender->getArmorInventory()->clearAll();
                $sender->setMaxHealth(20);
                $sender->setHealth(20);
                Utils::sendLobbyItems($sender);
            }else{
                $sender->sendMessage(Prefix::DEFAULT."You aren't in a match nor are you queueing.");
            }
        }
        return true;
    }
}