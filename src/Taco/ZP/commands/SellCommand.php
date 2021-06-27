<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class SellCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("sell", $owner);
        $this->setDescription("Sell all of your items!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if ($sender instanceof Player) {
            if (empty($args[0])) {
                $sender->sendMessage($this->getUsage());
                return true;
            }
            $give = 0;
            $m = Loader::getInstance()->playerData[$sender->getName()]["multiplier"];
            switch(strtolower($args[0])) {
                case "all":
                case "inv":
                    foreach ($sender->getInventory()->getContents() as $item) {
                        $asFor = $item->getId().":".$item->getDamage();
                        if (isset(Loader::SELL_PRICES[$asFor])) {
                            $e = Loader::SELL_PRICES[$asFor] * $item->getCount();
                            $give += $e * $m;
                            $sender->getInventory()->remove($item);
                        }
                    }
                    if ($give == 0) {
                        $sender->sendMessage(TF::RED."You do not have any items in your inventory to sell!");
                        return true;
                    }
                    $sender->sendMessage(TF::GREEN."Successfully sold items in inventory for $$give!");
                    Loader::getInstance()->economyAPI->addMoney($sender, $give);
                    break;
                case "hand":
                    $item = $sender->getInventory()->getItemInHand();
                    $asFor = $item->getId().":".$item->getDamage();
                    if (isset(Loader::SELL_PRICES[$asFor])) {
                        $e = Loader::SELL_PRICES[$asFor] * $item->getCount();
                        $give += $e * $m;
                        $sender->sendMessage(TF::GREEN."Successfully sold items in hand for $$give!");
                        Loader::getInstance()->economyAPI->addMoney($sender, $give);
                        $sender->getInventory()->remove($item);
                    } else {
                        $sender->sendMessage(TF::RED."You cannot sell this item!");
                    }
                    break;
            }
        }
        return true;
    }

    public function getUsage(): string
    {
        $arr = [
            "Sell Help",
            "/sell all/inv - Sells all items in your inventory.",
            "/sell hand - Sells item in your hand"
        ];
        $m = "";
        foreach ($arr as $a) {
            $m .= $a."\n";
        }
        return $m;
    }

}