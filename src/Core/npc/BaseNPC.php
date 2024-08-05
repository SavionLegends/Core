<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 5/13/2017
 * Time: 6:07 PM
 */

namespace Core\npc;

use pocketmine\Player;

use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;

use pocketmine\entity\Entity;

class BaseNPC{


    public static function makeNBT(Player $player, string $name): CompoundTag {
        $nbt = Entity::createBaseNBT($player, null, $player->getYaw(), $player->getPitch());
        $nbt->setShort("Health", 1);
        $nbt->setString("MenuName", "");
        $nbt->setString("CustomName", $name);
      //TODO: fix npcs!
        $player->saveNBT();
        return $nbt;
    }

}
