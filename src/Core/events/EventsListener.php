<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 4/27/2017
 * Time: 5:12 PM
 */

namespace Core\events;

use Core\Main;

use Core\managers\GameManager;
use Core\npc\HumanNPC;
use Core\player\PlayerClass;
use Core\utils\Permissions;
use Core\utils\Prefix;
use Core\utils\Utils;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\lang\Translatable;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EventsListener implements Listener{

    private $plugin;
    private array $pos1;
    private array $pos2;

    /**
     * EventsListener constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin){
        $this->plugin = $plugin;
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
        return $this->getPlugin()->getServer();
    }

    /**
     * @param PlayerCreationEvent $ev
     * @priority HIGHEST
     */
    public function setPlayerClass(PlayerCreationEvent $ev){
        $ev->setPlayerClass(PlayerClass::class);
    }

    /**
     * @param PlayerMoveEvent $ev
     * @return bool
     */
    public function onMove(PlayerMoveEvent $ev): bool{
        $player = $ev->getPlayer();
        if($player instanceof PlayerClass){
            if ($player->isRegistered() && !$player->isLoggedIn()) {
                $ev->cancel();
                return false;
            }
            if (!$player->isRegistered() && !$player->isLoggedIn()) {
                $ev->cancel();
                return false;
            }
            if (!$player->isRegistered()) {
                $ev->cancel();
                return false;
            }
            if($player->getMatch() === null){
                return false;
            }
            if($player->isQueued() && $player->getMatch()->getStatus() === GameManager::WAITING){
                $ev->cancel();
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * @param PlayerInteractEvent $ev
     */
    public function onInteract(PlayerInteractEvent $ev){
        $player = $ev->getPlayer();
        $username = $player->getName();
        $block = $ev->getBlock();
        if(isset($this->getPlugin()->isSetting[$player->getName()])){
            switch($this->getPlugin()->isSetting[$username]["int"]){
                case 0:
                    $this->pos1 = ["x" => $block->getPosition()->getX(),
                        "y" => $block->getPosition()->getY(),
                        "z" => $block->getPosition()->getZ(),
                        "level" => $block->getPosition()->getWorld()->getFolderName()];
                    $this->getPlugin()->isSetting[$username]["int"]++;
                    $player->sendMessage(Prefix::DEFAULT."Position one set please select the next!");
                    break;
                case 1:
                    $this->pos2 = ["x" => $block->getPosition()->getX(),
                        "y" => $block->getPosition()->getY(),
                        "z" => $block->getPosition()->getZ(),
                        "level" => $block->getPosition()->getWorld()->getFolderName()];
                    $player->sendMessage(Prefix::DEFAULT."Done! All positions set!");
                    $this->getPlugin()->newMatch($this->pos1, $this->pos2, $this->getPlugin()->isSetting[$username]["type"]);
                    unset($this->getPlugin()->isSetting[$username]);
                    break;
            }
        }
        if($ev->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
            if($player->getInventory()->getItemInHand()->getTypeId() === ItemTypeIds::MUSHROOM_STEW){
                if($player->getHealth() == $player->getMaxHealth()){
                    return;
                }else{
                    $item = $player->getInventory()->getItemInHand();
                    $item->setCount($item->getCount() - 1);
                    $player->getInventory()->setItemInHand($item);
                    $player->setHealth(($player->getHealth() + 1.5));
                }
            }
            //TODO: join games via items in inventory!( if i cant fix npcs :( )
            if($player instanceof PlayerClass){
                if(!$player->isLoggedIn()){
                    $player->sendMessage(Prefix::DEFAULT_BAD."Please log in to run commands!");
                    return;
                }
                if($player->isQueued()){
                    return;
                }
                $inventory = $player->getInventory();
                if($inventory->getItemInHand()->getTypeId() === ItemTypeIds::WOODEN_SWORD){
                    $this->getPlugin()->getServer()->dispatchCommand($player, "kohi join");
                }else if($inventory->getItemInHand()->getTypeId() === ItemTypeIds::STONE_SWORD){
                    $this->getPlugin()->getServer()->dispatchCommand($player, "ironsoup join");
                }else if($inventory->getItemInHand()->getTypeId() === ItemTypeIds::IRON_SWORD){
                    $this->getPlugin()->getServer()->dispatchCommand($player, "gapple join");
                }else{
                    if($inventory->getItemInHand()->getTypeId() === ItemTypeIds::GOLDEN_SWORD){
                        $this->getPlugin()->getServer()->dispatchCommand($player, "buhc join");
                    }
                }
            }
        }
    }

    /**
     * @param PlayerJoinEvent $ev
     */
    public function onJoin(PlayerJoinEvent $ev){
        $player = $ev->getPlayer();
        $ev->setJoinMessage(new Translatable(""));
        Main::getInstance()->loggedIn[$player->getName()] = true;
        $player->sendMessage(Prefix::DEFAULT."SERVER IS UNDER WORK");
        $player->sendMessage(Prefix::LOGGED_IN);
        if($player instanceof PlayerClass){
            Utils::sendLobbyItems($player);
        }else{
            $player->close("", TextFormat::RED . "Kicked due to " . $player->getName() . " not being a PlayerClass interface!\nPlease try again!");
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onEntityDamage(EntityDamageEvent $event){
        $entity = $event->getEntity();
        /*if($event instanceof EntityDamageByEntityEvent){
            $damager = $event->getDamager();
            if($damager instanceof Player){
                if ($entity instanceof HumanNPC && !isset($this->getPlugin()->npcDelete[$damager->getName()])){
                    if($entity->getNameTag() === "Kohi1v1"){
                        $this->getPlugin()->getServer()->dispatchCommand($damager, "kohi join");
                    }
                    if($entity->getNameTag() === "IronSoup1v1"){
                        $this->getPlugin()->getServer()->dispatchCommand($damager, "ironsoup join");
                    }
                    if($entity->getNameTag() === "BUHC1v1"){
                    $this->getPlugin()->getServer()->dispatchCommand($damager, "buhc join");
                    }
                    $event->setCancelled(true);
                    return;
                }
                if(isset($this->getPlugin()->npcDelete[$damager->getName()]) && $entity instanceof HumanNPC){
                    $entity->kill();
                    $entity->despawnFromAll();
                    $damager->sendMessage(Prefix::DEFAULT."Entity deleted!");
                    unset($this->getPlugin()->npcDelete[$damager->getName()]);
                    return;
                }
            }
        }*/
        if($event instanceof EntityDamageByEntityEvent){
            $damager = $event->getDamager();
            if($entity->getHealth() - $event->getFinalDamage() <= 0){
                $event->cancel();
                if($damager instanceof PlayerClass){
                    if($damager->isQueued()){
                        $damager->getMatch()->win($damager);
                    }
                }
            }
        }
    }

    /**
     * @param PlayerQuitEvent $ev
     */
    public function onQuit(PlayerQuitEvent $ev){
        $player = $ev->getPlayer();
        $ev->setQuitMessage(new Translatable(""));
        if($player instanceof PlayerClass){
            if(isset($this->getPlugin()->tempPass[$player->getName()])) unset($this->getPlugin()->tempPass[$player->getName()]);
            if($player->isLoggedIn()){
                $player->logout();
            }
            if($player->isQueued()){
                $player->setQueued(false, false, null);
                if($player->getMatch() !== null){
                    foreach ($player->getMatch()->getPlayers() as $name){
                        $player = $this->getServer()->getPlayerExact($name);
                        if($player instanceof PlayerClass){
                            $player->sendMessage(Prefix::DEFAULT . "Match ended due to other player leaving!");
                            $player->getMatch()->end();
                            $player->getMatch()?->removePlayer($player);
                        }
                    }
                }
            }
        }
    }

   /**
    public function onCommandPreProcess(PlayerCommandPreprocessEvent $ev){
        $player = $ev->getPlayer();
        if($player instanceof PlayerClass){
           /* if($player->isLoggedIn() === false){
                $player->sendMessage(Prefix::DEFAULT_BAD."Please log in to run commands!");
                $ev->setCancelled(true);
                return;
            }
            if($player->isQueued() && $player->inGame === true){
                if(strpos($ev->getMessage(), "/quit", 0) !== true){
                    $player->sendMessage(Prefix::DEFAULT."Please use /quit to quit matches!");
                    $ev->setCancelled(true);
                }
            }
            if($player->isLoggedIn() === false){
                if(strpos($ev->getMessage(), "/", 0) !== false){
                    $player->sendMessage(Prefix::DEFAULT."Please login to use commands!");
                    $ev->setCancelled(true);
                }
            }
        }
    }
    */
   /* public function onDeath(PlayerDeathEvent $ev){
        $ev->setDeathMessage(null);
        $entity = $ev->getEntity();
        if($ev instanceof EntityDamageByEntityEvent){
            $cause = $entity->getLastDamageCause();
            if($entity instanceof Player){
                $player = $entity;
                if($player instanceof PlayerClass){
                    $killer = $cause->getDamager();
                }
            }
        }
    }*/

    /**
     * @param CommandEvent $ev
     * @return void
     */
    public function onCommand(CommandEvent $ev){
        var_dump($ev->getCommand());
        $sender = $ev->getSender();
        if($sender instanceof PlayerClass){
            if($sender->isLoggedIn() === false){
                if(str_contains($ev->getCommand(), "/")){
                    $sender->sendMessage(Prefix::DEFAULT."Please login to use commands!");
                    $ev->cancel();
                }
            }
        }
    }

    /**
     * @param PlayerChatEvent $ev
     */
    public function onChat(PlayerChatEvent $ev){
        $player = $ev->getPlayer();
        $message = $ev->getMessage();
        if($player instanceof PlayerClass){
            $database = $this->getPlugin()->database->getAll();
            if($player->isLoggedIn() && $player->isRegistered()){
                foreach(Main::$badwords as $badword){
                    if(strpos(strtolower($message), strtolower($badword)) === true){
                        $new_message = str_replace($badword, TextFormat::BLACK."****", $message);
                        $ev->setMessage($new_message);
                    }
                }
            }
            if($player->isQueued() && $player->inGame === true){
                if(strpos($ev->getMessage(), "/quit", 0) !== true){
                    $player->sendMessage(Prefix::DEFAULT."Please use /quit to quit matches!");
                    $ev->cancel();
                }
            }
            if($player instanceof PlayerClass && $player->isLoggedIn()){
                $database = $this->getPlugin()->database->getAll();
                $rank = $database[strtolower($player->getName())]["rank"];
                $ev->setMessage($this->getPlugin()->getChatRank($rank)."".TextFormat::RESET.TextFormat::WHITE.$message);
            }
          /*  if($player->isRegistered() && $player->isLoggedIn()){
                if($this->getPlugin()->hash($player->getName(), $message) === $player->getPassword()){
                    $player->sendMessage(Prefix::PASSWORD_IN_CHAT);//password in chat
                    $ev->cancel();
                }
            } */
            /*if(!$player->isLoggedIn() && $player->isRegistered()){
                if($this->getPlugin()->hash($player->getName(), $message) === $database[strtolower($player->getName())]["password"]){
                    $player->login();
                }else{
                    $player->sendMessage(Prefix::PASSWORD_INCORRECT);
                    if(!isset($player->loginTrys[$player->getName()])) $player->loginTrys[$player->getName()] = 0;
                    $player->loginTrys[$player->getName()]++;
                    if($player->loginTrys[$player->getName()] === 5){
                        $player->close(" ",Prefix::DEFAULT."You have been kicked for reaching the max login attempt!");
                    }
                }
                $ev->cancel();
            }*/
         /*   if(!$player->isRegistered() && !$player->isLoggedIn()){
                if(isset($this->getPlugin()->tempPass[$player->getName()]) && $message === $this->getPlugin()->tempPass[$player->getName()]){
                    $database[strtolower($player->getName())] = [];
                    $database[strtolower($player->getName())]["password"] = $this->getPlugin()->hash($player->getName(), $this->getPlugin()->tempPass[$player->getName()]);
                    $database[strtolower($player->getName())]["uuid"] = $player->getUniqueId()->toString();
                    $database[strtolower($player->getName())]["time"] = date("D, F d, Y, H:i T");
                    $database[strtolower($player->getName())]["rank"] = "default";
                    $database[strtolower($player->getName())]["uploaded_to_sql"] = false;
                    $database[strtolower($player->getName())]["name"] = strtolower($player->getName());
                    $database[$player->getName()]["lang"] = "en";
                    $database[strtolower($player->getName())]["permissions"][Permissions::DEFAULT] = true;
                    $database[strtolower($player->getName())]["permissions"][Server::BROADCAST_CHANNEL_USERS] = true;
                    $this->getPlugin()->database->setAll($database);
                    $this->getPlugin()->database->save();
                    $this->getPlugin()->database->reload();
                    unset($this->getPlugin()->tempPass[$player->getName()]);
                    $player->login();//login via password
                    $ev->cancel();
                    unset($this->getPlugin()->tempPass[$player->getName()]);
                }
                if(!isset($this->getPlugin()->tempPass[$player->getName()]) && !$player->isRegistered() && !$player->isLoggedIn()){
                    $this->getPlugin()->tempPass[$player->getName()] = $message;
                    $player->sendMessage(Prefix::DEFAULT."Please type your password one more time");
                    $ev->cancel();
                }
                if(isset($this->getPlugin()->tempPass[$player->getName()]) && $message !== $this->getPlugin()->tempPass[$player->getName()] && !$player->isRegistered() && $player->isLoggedIn()){
                    $player->sendMessage(Prefix::DEFAULT."Passwords didn't match try again!");
                    $ev->cancel();
                }
            } */
        }
    }

    /**
     * @param BlockBreakEvent $ev
     * @return bool
     */
    public function onBreak(BlockBreakEvent $ev){
        $player = $ev->getPlayer();
        $block = $ev->getBlock();
        if($player instanceof PlayerClass){
            if(!$player->hasPermission(Permissions::BUILD) || !$player->hasPermission(Permissions::ALL_PERMS)){
                $ev->cancel();
                return false;
            }
        }
    }

    /**
     * @param BlockPlaceEvent $ev
     * @return bool
     */
    public function onPlace(BlockPlaceEvent $ev){
        $player = $ev->getPlayer();
        if ($player instanceof PlayerClass) {
            if (!$player->hasPermission(Permissions::PLACE) || !$player->hasPermission(Permissions::ALL_PERMS)){
                $ev->cancel();
                return false;
            }
        }
    }
}