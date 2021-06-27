<?php namespace Taco\ZP\leaderboards;

use pocketmine\entity\Animal;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class LeaderboardEntity extends Animal {

    public const NETWORK_ID = self::CHICKEN;

    public $width = 0.00;

    public $height = 0.00;

    private string $type = "";

    private Sorter $sorter;

    private int $updateTick = 0;

    private int $state = 0;

    public function getName() : string {
        return "Chicken";
    }

    public function tryChangeMovement() : void {}


    public function attack(EntityDamageEvent $source) : void {
        $source->setCancelled(true);
    }


    public function __construct(Level $level, CompoundTag $nbt, string $type=null) {
        if (!$type) return;
        parent::__construct($level, $nbt);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTagVisible(true);
        $this->setGenericFlag(Entity::DATA_FLAG_MOVING, false);
        $this->setGenericFlag(Entity::DATA_FLAG_SILENT);
        $this->type = $type;
        $this->setScale(0.0001);
        $this->setNameTagVisible();
        $this->setNameTagAlwaysVisible();
        $this->propertyManager->setFloat(self::DATA_BOUNDING_BOX_WIDTH, 0);
        $this->propertyManager->setFloat(self::DATA_BOUNDING_BOX_HEIGHT, 0);
        $this->sorter = new Sorter();
    }


    public function entityBaseTick(int $tickDiff = 1) : bool {
        $this->updateTick++;
        if ($this->updateTick > 2400) {
            $this->updateTick = 0;
            switch ($this->type) {
                case "money":
                    $this->setNameTag($this->sorter->getEconomyLeaderboard());
                    break;
                case "gangs":
                    $this->setNameTag($this->sorter->getClanKills());
                    break;
            }
        }
        return parent::entityBaseTick($tickDiff);
    }

}