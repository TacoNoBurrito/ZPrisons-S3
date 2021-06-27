<?php namespace Taco\ZP\randomEntities;

use pocketmine\entity\Animal;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class DamageEntity extends Animal {

    public const NETWORK_ID = self::CHICKEN;

    public $width = 0.00;

    public $height = 0.00;

    private int $time = 0;

    private $player = null;

    public function getName(): string
    {
        return "Chicken";
    }

    public function tryChangeMovement(): void
    {

    }


    public function attack(EntityDamageEvent $source): void
    {
        $source->setCancelled(true);
    }


    public function __construct(Level $level, CompoundTag $nbt, Player $player=null, string $damage)
    {
        if (!$player) return;
        parent::__construct($level, $nbt);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTagVisible(true);
        $this->setGenericFlag(Entity::DATA_FLAG_MOVING, false);
        $this->setGenericFlag(Entity::DATA_FLAG_SILENT);
        $this->player=$player;
        $this->setScale(0.0001);
        $this->setNameTagVisible();
        $this->setNameTagAlwaysVisible();
        $this->propertyManager->setFloat(self::DATA_BOUNDING_BOX_WIDTH, 0);
        $this->propertyManager->setFloat(self::DATA_BOUNDING_BOX_HEIGHT, 0);
        $this->setNameTag($damage);

    }


    public function entityBaseTick(int $tickDiff = 1): bool
    {
        $this->teleport(new Vector3($this->getX(), $this->getY() + 0.01, $this->getZ()));
        $this->updateMovement(true);
        $this->time++;
        if($this->time > 60) {
            $this->flagForDespawn();
            return $this->isAlive();
        }
        return parent::entityBaseTick($tickDiff);
    }




}