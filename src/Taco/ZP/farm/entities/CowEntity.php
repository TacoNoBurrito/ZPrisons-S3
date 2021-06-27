<?php namespace Taco\ZP\farm\entities;

use pocketmine\entity\Animal;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use Taco\ZP\Loader;

class CowEntity extends Animal {

    const NETWORK_ID = self::COW;

    public $width = 0.6;
    public $height = 1.8;

    private bool $isA = true;

    public function getType() : string {
        return "Cow";
    }

    public function getName() : string{
        return "Cow";
    }

    public function getNameTag() : string {
        return "Cow";
    }

    public function getDrops() : array {
        return [
            Item::get(ItemIds::BEEF, 0, mt_rand(1,3)),
            Item::get(ItemIds::LEATHER, 0, mt_rand(1,3))
        ];
    }

    public function attack(EntityDamageEvent $source) : void {
        if ($this->isA) {
        if ($source instanceof EntityDamageByEntityEvent) {
            if ($source->getDamager() instanceof Player) {
                $this->kill();
                $this->isA = false;
                $this->getLevel()->dropExperience($this, 3);
                Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                    function (int $currentTick): void {
                        $pos = Loader::getFarmUtils()->generateRandomSpawnPoint();
                        $nbt = Entity::createBaseNBT($pos, null, 0, 0);
                        $entity = new CowEntity(Loader::getInstance()->getServer()->getLevelByName("farm"), $nbt);
                        $entity->spawnToAll();
                    }
                ), 20 * 3);
                return;
            }
        }
        }
        $source->setCancelled(true);
    }

    public function tryChangeMovement():void{}

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->setGenericFlag(Entity::DATA_FLAG_SILENT, true);
        $this->setGenericFlag(Entity::DATA_FLAG_MOVING, false);
    }

    public function entityBaseTick(int $tickDiff = 1) : bool {return parent::entityBaseTick($tickDiff);}

}