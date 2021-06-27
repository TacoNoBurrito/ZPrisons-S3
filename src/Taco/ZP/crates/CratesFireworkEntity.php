<?php namespace Taco\ZP\crates;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

class CratesFireworkEntity extends Entity {

    public const NETWORK_ID = Entity::FIREWORKS_ROCKET;

    public $width = 0.001;
    public $height = 0.001;

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
    }

    public function entityBaseTick(int $tickDiff = 1) : bool {
        $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_BLAST);
        $this->broadcastEntityEvent(ActorEventPacket::FIREWORK_PARTICLES);
        $this->flagForDespawn();
        return parent::entityBaseTick($tickDiff);
    }

}