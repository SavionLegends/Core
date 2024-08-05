<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 4/29/2017
 * Time: 12:28 PM
 */

namespace Core\commands;

use Core\commands\custom\LobbyCommand;
use Core\commands\custom\NPCCommand;
use Core\commands\custom\PermissionCommand;
use Core\commands\custom\QuitCommand;
use Core\commands\custom\TestCommand;
use Core\commands\game\BUHCCommand;
use Core\commands\game\GappleCommand;
use Core\commands\game\IronSoupCommand;
use Core\commands\game\KohiCommand;
use pocketmine\command\CommandMap;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Core\commands\custom\LangCommand;
use Core\commands\override\MeCommand;
use Core\commands\override\TellCommand;
use Core\commands\override\HelpCommand;
use Core\commands\custom\StatsCommand;
use pocketmine\command\PluginIdentifiableCommand;

use Core\Main;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;

class CoreCommand extends Command{

    private $plugin;


    /**
     * @param Main $plugin
     * @param string $name
     * @param Translatable|string $description
     * @param Translatable|string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
    }


    public function getPlugin(): Main{
        return $this->plugin;
    }

    /**
     * @param Main $main
     * @param CommandMap $map
     */
    public static function registerAll(Main $main, CommandMap $map){
        $cmds = ["tell","help","me","?","msg"];
        foreach($cmds as $cmd){
            self::unregisterCommand($map, $cmd);
        }
        $map->registerAll("c", [
            new TellCommand($main, "tell", "Send a player a private message", null),
            new LangCommand($main, "lang", "Change your language preference!", null),
            new MeCommand($main, "me", "Shout yourself out!", null),
            new HelpCommand($main, "help", "See the list of commands!", null),
            new KohiCommand($main, "kohi","Command to set/edit/join kohi matches", null),
            new IronSoupCommand($main, "ironsoup","Command to set/edit/join ironsoup matches", null),
            new QuitCommand($main, "quit", "Command to quit certain events", null),
            new StatsCommand($main, "stats", "Command to check your stats and other players stats!", null),
            new LobbyCommand($main, "lobby", "Go back to the lobby!", null, ["/spawn"]),
            new PermissionCommand($main, "permission", "set/remove/set rank command!", null),
            new TestCommand($main,"test","Test command for testing purposes.",null),
            new GappleCommand($main, "gapple", "Command to set/edit/join gapple matches", null),
            new BUHCCommand($main, "buhc", "Command to set/edit/join buhc matches", null)]);
    }

    /**
     * @param CommandMap $map
     * @param $name
     * @return bool
     */
    public static function unregisterCommand(CommandMap $map, $name): bool{
        $cmd = $map->getCommand($name);
        if($cmd instanceof Command){
            $cmd->setLabel($name . "_disabled");
            $cmd->unregister($map);
            return true;
        }
        return false;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if(parent::testPermission($sender) === false){
            return false;
        }else{
            $result = $this->onExecute($sender, $args);
            if(is_string(strtolower($result))){
                $sender->sendMessage($result);
            }
            return true;
        }
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public function onExecute(CommandSender $sender, array $args): bool{
        if(parent::testPermission($sender) === false){
            return false;
        }
        return true;
    }
}