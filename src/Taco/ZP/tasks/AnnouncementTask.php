<?php namespace Taco\ZP\tasks;

use pocketmine\scheduler\Task;
use Taco\ZP\Loader;

class AnnouncementTask extends Task {

    public const discord = "bit.ly/zpdisc1";

    private array $announcements = [
        "§7[§l§dZ§bPrisons§r§7] §rPlease join our discord at: §e".self::discord,
        "§7[§l§dZ§bPrisons§r§7] §rAsk a staff for help to get any answers!",
        "§7[§l§dZ§bPrisons§r§7] §rMake a ticket in the discord to report a hacker!",
        "§7[§l§dZ§bPrisons§r§7] §rClicking 20+ cps can result in a ban!",
        "§7[§l§dZ§bPrisons§r§7] §rApply for staff at our discord! §e".self::discord,
        "§7[§l§dZ§bPrisons§r§7] §rVote to earn great rewards at §ebit.ly/zpvote"
    ];

    public function onRun(int $currentTick) : void {
        Loader::getInstance()->getServer()->broadcastMessage($this->announcements[array_rand($this->announcements)]);
    }

}