<?php namespace Taco\ZP\npc;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\Villager;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use Taco\ZP\Loader;

class NPCEntity extends Villager {

    const NETWORK_ID = self::VILLAGER;

    public $width = 0.6;
    public $height = 1.8;

    private string $customName = "";
    private string $customCommand = "";
    private bool $isConsoleCommand = false;

    private $lookcd = 0;

    public function getType() : string {
        return "Villager";
    }

    public function getName() : string{
        return "Villager";
    }

    public function getNameTag() : string {
        return $this->customName;
    }

    public function getCommand() : string {
        return $this->customCommand;
    }

    public function isConsoleCommand() : bool {
        return $this->isConsoleCommand;
    }

    public function attack(EntityDamageEvent $source) : void {
        $source->setCancelled(true);
        parent::attack($source);
        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();
            if ($damager instanceof Player) {
                $cmd = str_replace("{player}", $damager->getName(), $this->getCommand());
                if ($this->isConsoleCommand()) {
                    Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
                } else {
                    Loader::getInstance()->getServer()->dispatchCommand($damager, $cmd);
                }
            }
        }
    }

    public function tryChangeMovement():void{}

    public function __construct(Level $level, CompoundTag $nbt, string $name=null, string $command=null, bool $isConsoleCommand=null) {
        if ($name==null) return;
        parent::__construct($level, $nbt);
        $this->customName = $name;
        $this->customCommand = $command;
        $this->isConsoleCommand = $isConsoleCommand;
        $this->setNameTagVisible(true);
        $this->setNameTag($name);
        $this->setNameTagAlwaysVisible(true);
        $this->setGenericFlag(Entity::DATA_FLAG_SILENT, true);
        $this->setGenericFlag(Entity::DATA_FLAG_MOVING, false);
        $this->setCanSaveWithChunk(true);
    }

    public function entityBaseTick(int $tickDiff = 1) : bool {
        //is laggy because of getPlayer() looping. Look at slapperrotation in future
        $this->lookcd ++;
        if ($this->lookcd > 10) {
            $this->lookcd = 0;
            $players = [];
            foreach ($this->getLevel()->getPlayers() as $player) {
                $players[$player->getName()] = $player->distance($this);
            }
            if (count($players) > 0) {
                arsort($players);
                foreach ($players as $name => $bruh) {
                    $this->lookAt(Loader::getInstance()->getServer()->getPlayer($name));
                    $this->lookAt(Loader::getInstance()->getServer()->getPlayer($name));
                }
            }
        }
        return parent::entityBaseTick($tickDiff);
    }

}