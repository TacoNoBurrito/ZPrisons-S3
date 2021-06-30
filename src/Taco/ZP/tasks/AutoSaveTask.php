<?php namespace Taco\ZP\tasks;

use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use Taco\ZP\Loader;
use function count;

class AutoSaveTask extends Task {

    public function onRun(int $currentTick) : void {
        $players = [];
        foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            $players[] = $player->getName();
        }
        if (count($players) < 1) return;
        Loader::getInstance()->getServer()->broadcastMessage("§cPerforming auto-save. Approximated time: ".(count($players) * 1.5)."s. The server may lag during this time.");
        $i = 1;
        foreach ($players as $player) {
            $playerr = Loader::getInstance()->getServer()->getPlayer($player);
            if ($playerr == null) continue;
            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(static function(int $currentTick) use ($playerr) : void{
                if (!$playerr == null) {
                    $playerr->sendMessage("§cSystem >> Your data was backed up.");
                    Loader::getUtils()->saveData($playerr);
                }
            }), $i * 20);
            $i++;
        }
        $this->getHandler()->cancel();
    }

}