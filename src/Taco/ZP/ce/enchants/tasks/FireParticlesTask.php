<?php namespace Taco\ZP\ce\enchants\tasks;

use pocketmine\level\particle\FlameParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class FireParticlesTask extends Task {

    private Player $player;

    private int $timesRan = 0;

    private int $yaw = 0;

    public function __construct(Player $player) {
        $this->player = $player;
    }

    public function onRun(int $currentTick) : void {
        if ($this->player == null) {
            $this->getHandler()->cancel();
            return;
        }
        if ($this->timesRan > 3) {
            $this->player->setImmobile(false);
            $this->getHandler()->cancel();
            return;
        }
        $level = $this->player->getLevel();
        $x = $this->player->getX();
        $y = $this->player->getY();
        $z = $this->player->getZ();
        $center = new Vector3($x, $y, $z);
        for($yaw = 0; $yaw <= 10; $yaw += (M_PI * 2) / 20) {
            $x = -sin($yaw) + $center->x;
            $z = cos($yaw) + $center->z;
            $y = $center->y;
            try {
                $level->addParticle(new FlameParticle(new Vector3($x, $y + 1.5, $z)));
            } catch (\Error $ex) {
                $this->getHandler()->cancel();
                return;
            }
        }
        $this->timesRan++;
    }

}