<?php namespace Taco\ZP\tasks;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\entity\Villager;
use pocketmine\scheduler\Task;
use Taco\ZP\ft\FloatingTextEntity;
use Taco\ZP\ft\FloatingTextUtils;
use Taco\ZP\Loader;

class TimeTask extends Task {

    private int $mineReset = 0;

    private int $timeSet = 0;

    private int $entityReload = 0;

    public function onRun(int $currentTick) : void {
        $this->timeSet++;
        $this->mineReset++;
        $this->entityReload++;
        if ($this->timeSet > 120) {
            $this->timeSet = 0;
            Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "time set day");
        }
        if ($this->mineReset > 1800) {
            $this->mineReset = 0;
            Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "mine reset-all");
            foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
                if (Loader::getUtils()->getMineByPosition($player) !== null) Loader::getUtils()->teleportToSpawn($player);
            }
            Loader::getInstance()->getServer()->broadcastMessage("§7[§l§dZ§bPrisons§r§7] §rAll mines have been reset");
        }
        if ($this->entityReload > 1800) {
            $this->entityReload = 0;
            foreach(Loader::getInstance()->getServer()->getDefaultLevel()->getEntities() as $entity) {
                if ($entity instanceof FloatingTextEntity or $entity instanceof Villager) {
                    $entity->flagForDespawn();
                }
            }
            $ftU = new FloatingTextUtils();
            $ftU->register();
            Loader::getNPCUtils()->loadNPCs();
        }
    }

}