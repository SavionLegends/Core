<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 9/14/2017
 * Time: 6:07 PM
 */

namespace Core\commands\custom;

use Core\Main;
use Core\commands\CoreCommand;
use Core\npc\BaseNPC;
use Core\player\PlayerClass;
use Core\utils\Permissions;
use Core\utils\Prefix;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;

class NPCCommand extends CoreCommand{

    private $plugin, $server, $name;

    public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
        parent::__construct($plugin, $name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->setPermission(Permissions::NPC_COMMAND);
    }

    public function getPlugin() : Main{
        return $this->plugin;
    }

    public function getServer(){
        return $this->server;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
     /*   if(!isset($args[0])){
            $sender->sendMessage(Prefix::DEFAULT."Please do /npc [args]!");
            return false;
        }
        if($sender instanceof PlayerClass){
            if($args[0] === "spawn" && $sender->){
                if(isset($args[1])){
                    if(strtolower($args[1]) === strtolower("Kohi1v1")){
                        $this->name = "Kohi1v1";
                    }
                    $nbt = BaseNPC::makeNBT($sender, $this->name);
                    $entity = Entity::createEntity("HumanNPC", $sender->getLevel(), $nbt);
                    $entity->spawnToAll();
                    $sender->sendMessage(Prefix::DEFAULT."NPC Spawned!");
                }
            }
            if($args[0] === "delete" && $sender->isOp()){
                $this->getPlugin()->npcDelete[$sender->getName()] = $sender->getName();
                $sender->sendMessage(Prefix::DEFAULT."Please hit the NPC you want to delete!");
            }
        } */
        return true;
    }
}