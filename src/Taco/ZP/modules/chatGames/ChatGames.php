<?php namespace Taco\ZP\modules\chatGames;

use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class ChatGames {

    public bool $canSolve = false;

    public string $toSolve = "";

    public function startGames() : void {
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ChatGamesTask(), 20);
    }

    public function win(Player $player) : void {
        $this->canSolve = false;
        $this->toSolve = "";
        $random = mt_rand(100,300);
        Loader::getInstance()->getServer()->broadcastMessage("§l§dCHAT§bGAMES §r§7>> §b".$player->getName()." has successfully unscrambled the word and has won §d$".$random."!");
        Loader::getInstance()->economyAPI->addMoney($player, $random);

    }

}