<?php

declare(strict_types=1);

namespace Core\npc;

use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\Player;

class HumanNPC extends Human {
    use NPCTraits;

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->prepareMetadata();
    }

    public function saveNBT() : CompoundTag{
        parent::saveNBT();
        $this->saveSlapperNbt();
    }

    public function sendNameTag(Player $player) {
        $pk = new SetEntityDataPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->metadata = [self::DATA_NAMETAG => [self::DATA_TYPE_STRING, $this->getDisplayName($player)]];
        $player->sendDataPacket($pk);
    }

    protected function sendSpawnPacket(Player $player): void {
        parent::sendSpawnPacket($player);

        if (($menuName = $this->namedtag->getString("MenuName", "", true)) !== "") {
            $player->getServer()->updatePlayerListData($this->getUniqueId(), $this->getId(), $menuName, $this->skin, "", [$player]);
        }
    }
}
