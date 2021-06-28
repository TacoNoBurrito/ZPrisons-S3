<?php namespace Taco\ZP\modules\chatGames;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class ChatGamesTask extends Task {

    private array $words = [
        "prison",
        "grind",
        "taco",
        "pickaxe",
        "discord",
        "pog",
        "dog",
        "cat",
        "fish",
        "youtube",
        "boost",
        "free",
        "boomyourbang",
        "tacoissuchacutieomghejustmakesmefeelsogoodwheneverhelogsonanditjustmakesmehappyandsmilewhenevermyfriendtacogetsonline"
    ];

    private int $time = 300;

    private int $solvingTime = 60;

    public function onRun(int $currentTick) : void {
        if (Loader::getChatGames()->canSolve) {
            $this->solvingTime--;
            if ($this->solvingTime < 1) {
                Loader::getInstance()->getServer()->broadcastMessage("§l§dCHAT§bGAMES §r§7>> §bNo one could unscramble the word: §d".Loader::getChatGames()->toSolve."§b and the game has ended.");
                Loader::getChatGames()->canSolve = false;
                $this->solvingTime = 60;
                $this->time = 0;
            }
        } else {
            $this->time++;
            if ($this->time > 300) {
                $word = $this->words[array_rand($this->words)];
                $shuffled = str_shuffle($word);
                Loader::getChatGames()->toSolve = $word;
                Loader::getInstance()->getServer()->broadcastMessage("§l§dCHAT§bGAMES §r§7>> §bUnscramble the word: §d".$shuffled." §bto win a prize!");
                Loader::getChatGames()->canSolve = true;
                $this->time = 0;
            }
        }
    }

}