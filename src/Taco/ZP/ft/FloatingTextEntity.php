<?php namespace Taco\ZP\ft;

use pocketmine\entity\Animal;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class FloatingTextEntity extends Animal {

    public const NETWORK_ID = self::CHICKEN;

    public $width = 0.00;

    public $height = 0.00;

    private array $states = [];

    private int $updateTick = 0;

    private int $state = 0;

    public function getName() : string {
        return "Chicken";
    }

    public function tryChangeMovement() : void {}


    public function attack(EntityDamageEvent $source) : void {
        $source->setCancelled(true);
    }


    public function __construct(Level $level, CompoundTag $nbt, array $states=null) {
        if (!$states) return;
        parent::__construct($level, $nbt);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTagVisible(true);
        $this->setGenericFlag(Entity::DATA_FLAG_MOVING, false);
        $this->setGenericFlag(Entity::DATA_FLAG_SILENT);
        $this->states = $states;
        $this->setScale(0.0001);
        $this->setNameTagVisible();
        $this->setNameTagAlwaysVisible();
        $this->propertyManager->setFloat(self::DATA_BOUNDING_BOX_WIDTH, 0);
        $this->propertyManager->setFloat(self::DATA_BOUNDING_BOX_HEIGHT, 0);
        $this->setCanSaveWithChunk(true);

    }


    public function entityBaseTick(int $tickDiff = 1) : bool {
        $this->updateTick++;
        if ($this->updateTick == 60) {
            $this->updateTick = 0;
            $this->state++;
            switch($this->state) {
                case 1:
                    $this->setNameTag($this->states[0]);
                    break;

                case 2:
                    $this->state = 0;
                    $this->setNameTag($this->states[1]);
                    break;
            }
        }
        return parent::entityBaseTick($tickDiff);
    }

}