<?php namespace Taco\ZP\tasks;

use pocketmine\scheduler\Task;
use Taco\ZP\Loader;

class NametagTask extends Task {

    public function onRun(int $currentTick) : void {
        foreach(Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            Loader::getUtils()->updateNametag($player);
        }
    }

}