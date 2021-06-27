<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class RankupCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("rankup", $owner);
        $this->setDescription("Get to the highest ranks!");
        $this->setAliases(["ru"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        $money = Loader::getInstance()->economyAPI->myMoney($sender);
        $prestige = Loader::getInstance()->playerData[$sender->getName()]["prestige"];
        $rank = Loader::getInstance()->playerData[$sender->getName()]["rank"];
        if (Loader::getInstance()->playerData[$sender->getName()]["rank"] == "Z") {
            $sender->sendMessage(TF::RED."You are at the max rank! Please use /prestige to continue ranking up!");
            return true;
        }
        if (!empty($args[0])) {
            if (strtolower($args[0]) == "max") {
                $max = explode(":", Loader::getUtils()->getMaxRankup($rank, $money, $prestige));
                $newRank = $max[0];
                $price = (int)$max[1];
                if ($newRank == $rank) {
                    $sender->sendMessage(TF::RED . "You do not have enough money to rankup even once!");
                    return true;
                }
                if ($price > $money) {
                    $sender->sendMessage(TF::RED."You do not have enough money to do this.");
                    return true;
                }
                if ($price<1) {
                    $sender->sendMessage(TF::RED."error");
                    return true;
                }
                $sender->sendMessage(TF::GREEN."Successfully ranked up to ".$newRank."! Price: $".$price);
                Loader::getInstance()->economyAPI->reduceMoney($sender, $price);
                Loader::getInstance()->playerData[$sender->getName()]["rank"] = $newRank;
                Loader::getInstance()->getServer()->broadcastMessage(TF::BOLD.TF::GRAY."[".TF::RESET.TF::GOLD."RANKUP-MAX".TF::BOLD.TF::GRAY."] ".TF::RESET.TF::YELLOW.$sender->getName().TF::WHITE." has successfully ranked up to ".TF::YELLOW.$newRank."!");
            }
            return true;
        }
        $nextRank = Loader::getUtils()->n2l(Loader::getUtils()->l2n(Loader::getInstance()->playerData[$sender->getName()]["rank"])+1);
        $price = Loader::getUtils()->getPriceOfRank($nextRank, $prestige);
        if ($money >= $price) {
            Loader::getInstance()->economyAPI->reduceMoney($sender, $price);
            $sender->sendMessage(TF::GREEN."You have successfully ranked up!");
            Loader::getInstance()->getServer()->broadcastMessage(TF::BOLD.TF::GRAY."[".TF::RESET.TF::GOLD."RANKUP".TF::BOLD.TF::GRAY."] ".TF::RESET.TF::YELLOW.$sender->getName().TF::WHITE." has successfully ranked up to ".TF::YELLOW.$nextRank."!");
            Loader::getInstance()->playerData[$sender->getName()]["rank"] = $nextRank;
        } else {
            $needs = $price - $money;
            $sender->sendMessage(TF::RED."Sorry, but you need ".TF::GREEN."$$needs ".TF::RED."more to rankup.");
        }
        return true;
    }

}