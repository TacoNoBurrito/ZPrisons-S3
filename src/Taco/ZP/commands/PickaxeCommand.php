<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\utils\Utils;

class PickaxeCommand extends PluginCommand {

    private array $cooldown = [];

    public function __construct(Plugin $owner) {
        parent::__construct("pickaxe", $owner);
        $this->setDescription("Get a complimentary pickaxe!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender instanceof Player) return true;
        if (isset($this->cooldown[$sender->getName()]) and time() - $this->cooldown[$sender->getName()] < 30) {
            $t = time() - $this->cooldown[$sender->getName()];
            $t = 30 - $t;
            $sender->sendMessage(TF::RED."Your complimentary pickaxe is still on cooldown for ".Utils::intToString($t)."!");
        } else {
            $this->cooldown[$sender->getName()] = time();
            $sender->getInventory()->addItem(Item::get(ItemIds::DIAMOND_PICKAXE));
            $sender->sendMessage(TF::GREEN."Successfully recieved your complimentary pickaxe!");
        }
        return true;
    }

}