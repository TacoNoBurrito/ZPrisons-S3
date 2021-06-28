<?php namespace Taco\ZP\tasks;

use pocketmine\scheduler\Task;
use Taco\ZP\Loader;
use pocketmine\utils\TextFormat as TF;

class ScoreTagTask extends Task {

    public function onRun(int $currentTick) : void {
        foreach(Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if (!isset(Loader::getInstance()->playerData[$player->getName()])) continue;
            $player->setScoreTag(TF::GOLD."Blocks Broken".TF::GRAY.": ".TF::WHITE.Loader::getInstance()->playerData[$player->getName()]["blocksBroken"]);
        }
    }

}