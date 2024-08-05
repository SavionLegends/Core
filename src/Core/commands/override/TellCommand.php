<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 4/29/2017
 * Time: 12:34 PM
 */

namespace Core\commands\override;

use Core\Main;

use Core\player\PlayerClass;
use Core\utils\Permissions;
use Core\utils\Prefix;
use pocketmine\command\CommandSender;
use Core\commands\CoreCommand;
use pocketmine\lang\Translatable;
use pocketmine\Server;

class TellCommand extends CoreCommand{

    private Main $plugin;
    private Server $server;

    /**
     * @param Main $plugin
     * @param string $name
     * @param Translatable|string $description
     * @param Translatable|string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
        parent::__construct($plugin, $name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->setPermission(Permissions::TELL_COMMAND);
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
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if(isset($args[0])){
            $player = $this->getServer()->getPlayerExact($args[0]);
            if($player === null){
                $sender->sendMessage(Prefix::PLAYER_NOT_ONLINE);
            }else{
                if($sender instanceof PlayerClass and $player instanceof PlayerClass){
                    $message = implode(" ", $args[1]);
                    $player->sendMessage(Prefix::DEFAULT.$sender->getRealName()."->You ".$message);
                    $sender->sendMessage(Prefix::DEFAULT."You messaged ".$player->getRealName().": ".$message);
                    $privateMessage = date("D, F d, Y, H:i T")." ".$sender->getRealName()."->".$player->getRealName().": ".$message;
                    $this->getPlugin()->privateMessages->set($privateMessage);
                    $this->getPlugin()->privateMessages->save();
                }
            }
        }
        return true;
    }

}