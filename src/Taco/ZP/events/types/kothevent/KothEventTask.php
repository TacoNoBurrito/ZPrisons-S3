<?php namespace Taco\ZP\events\types\kothevent;

use pocketmine\scheduler\Task;
use Taco\ZP\Loader;

class KothEventTask extends Task {

    public string $kothPos1 = "193:61:133";

    public string $kothPos2 = "180:80:120";

    public function onRun(int $currentTick) : void {
        $player = Loader::getEventManager()->capping;
        if (!$player == "") {
            $capping = Loader::getInstance()->getServer()->getPlayer($player);
            if ($capping == null) {
                Loader::getEventManager()->capping = "";
                Loader::getEventManager()->cappingTime = 0;
                return;
            }
            if (!Loader::getAreaUtils()->isWithinTwoFormattedPoints($capping, $this->kothPos1, $this->kothPos2)) {
                Loader::getEventManager()->capping = "";
                Loader::getEventManager()->cappingTime = 0;
                return;
            }
            Loader::getEventManager()->cappingTime += 1;
            if (Loader::getEventManager()->cappingTime > 100) {
                Loader::getEventManager()->endKothEvent();
                $this->getHandler()->cancel();
                return;
            }
        } else {
            foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
                if (Loader::getAreaUtils()->isWithinTwoFormattedPoints($player, $this->kothPos1, $this->kothPos2)) {
                    Loader::getEventManager()->capping = $player->getName();
                    break;
                }
            }
        }
    }

}