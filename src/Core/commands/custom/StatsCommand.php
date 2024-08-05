<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 6/14/2018
 * Time: 3:57 PM
 */

namespace Core\commands\custom;

use Core\commands\CoreCommand;
use Core\Main;
use Core\utils\Permissions;
use Core\utils\Prefix;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

class StatsCommand extends CoreCommand {
    /**
     * @var Main
     */
    private $plugin;
    /**
     * @var string
     */
    private $server;

    /**
     * StatsCommand constructor.
     * @param Main $plugin
     * @param string $name
     * @param null|string $desc
     * @param $usage
     * @param array $aliases
     */
    public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
        parent::__construct($plugin, $name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->setPermission(Permissions::STATS_COMMAND);
    }


    public function getPlugin() : Main{
        return $this->plugin;
    }

    /**
     * @return \pocketmine\Server
     */
    public function getServer(){
        return $this->server;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        $sender->sendMessage(Prefix::DEFAULT."In progress...");
        return true;
    }
}