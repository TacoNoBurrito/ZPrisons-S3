<?php namespace Taco\ZP\tasks;

use pocketmine\entity\object\ItemEntity;
use pocketmine\scheduler\Task;
use Taco\ZP\Loader;

class EntityClearTask extends Task {

    private int $time = 300;

    public function onRun(int $currentTick) : void {
        $this->time--;
        if (is_int($this->time / 60) and $this->time !== 0) {
            Loader::getInstance()->getServer()->broadcastMessage("§r§7[§4§lAnti-Lagg§r§7] §fAll ground entities are clearing in ".$this->time."s!");
        }
            switch($this->time) {
                case 30:
                    Loader::getInstance()->getServer()->broadcastMessage("§r§7[§4§lAnti-Lagg§r§7] §fAll ground entities are clearing in 30s!");
                    break;
                case 10:
                    Loader::getInstance()->getServer()->broadcastMessage("§r§7[§4§lAnti-Lagg§r§7] §fAll ground entities are clearing in 10s!");
                    break;
                case 3:
                    Loader::getInstance()->getServer()->broadcastMessage("§r§7[§4§lAnti-Lagg§r§7] §fAll ground entities are clearing in 3s!");
                    break;
                case 2:
                    Loader::getInstance()->getServer()->broadcastMessage("§r§7[§4§lAnti-Lagg§r§7] §fAll ground entities are clearing in 2s!");
                    break;
                case 1:
                    Loader::getInstance()->getServer()->broadcastMessage("§r§7[§4§lAnti-Lagg§r§7] §fAll ground entities are clearing in 1s!");
                    break;
                case 0:
                    $entities = 0;
                    foreach(Loader::getInstance()->getServer()->getLevels() as $level) {
                        foreach($level->getEntities() as $entity) {
                            if ($entity instanceof ItemEntity) {
                                $entity->flagForDespawn();
                                $entities++;
                            }
                        }
                    }
                    Loader::getInstance()->getServer()->broadcastMessage("§r§7[§4§lAnti-Lagg§r§7] §fCleared §e$entities §fentities.");
                    $this->time = 300;
                    break;

        }
    }

}