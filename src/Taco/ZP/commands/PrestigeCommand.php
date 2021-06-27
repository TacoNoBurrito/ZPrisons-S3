<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class PrestigeCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("prestige", $owner);
        $this->setDescription("Ascend to new levels!");
        $this->setAliases(["p"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        $money = Loader::getInstance()->economyAPI->myMoney($sender);
        $prestige = Loader::getInstance()->playerData[$sender->getName()]["prestige"];
        $rank = Loader::getInstance()->playerData[$sender->getName()]["rank"];
        if ($rank == "Z") {
            $price = 1000000 * $prestige + 500000;
            if ($money >= $price) {
                Loader::getInstance()->economyAPI->reduceMoney($sender, $price);
                $sender->sendMessage(TF::GREEN."Successfully prestiged!");
                Loader::getInstance()->playerData[$sender->getName()]["prestige"] += 1;
                $sender->teleport(Loader::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
                Loader::getInstance()->playerData[$sender->getName()]["rank"] = "A";
                $newMultiplier = (string)((float)Loader::getInstance()->playerData[$sender->getName()]["multiplier"] + 0.2);
                Loader::getInstance()->playerData[$sender->getName()]["multiplier"] = $newMultiplier;
                Loader::getInstance()->getServer()->broadcastMessage(TF::BOLD.TF::GRAY."[".TF::RESET.TF::GOLD."PRESTIGE".TF::BOLD.TF::GRAY."] ".TF::RESET.TF::YELLOW.$sender->getName().TF::WHITE." has successfully prestiged to ".TF::YELLOW.($prestige+1)."!");
            } else {
                $needs = $price - $money;
                $sender->sendMessage(TF::RED."You need ".TF::GREEN."$$needs".TF::RED." more to prestige!");
            }
        } else {
            $sender->sendMessage(TF::RED."You need to be the rank Z to prestige!");
        }
        return true;
    }

}