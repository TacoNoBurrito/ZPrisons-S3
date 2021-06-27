<?php namespace Taco\ZP\events\types\miningevent;

use pocketmine\scheduler\Task;
use Taco\ZP\Loader;

class MiningEventTask extends Task {

    public function onRun(int $currentTick) : void {
        Loader::getEventManager()->time += 1;
        foreach (Loader::getEventManager()->playing as $name => $mined) {
            $p = Loader::getInstance()->getServer()->getPlayer($name);
            if ($p == null) unset(Loader::getEventManager()->playing[$name]);
        }
        if (Loader::getEventManager()->time > 600) {
            Loader::getEventManager()->endMiningEvent();
            $this->getHandler()->cancel();
        }
    }

}