<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\item\Armor;
use pocketmine\item\Pickaxe;
use pocketmine\item\Sword;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\ce\CEMenu;

class UpgradeMenuCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("upgrade", $owner);
        $this->setDescription("Add custom enchants to your pickaxe.");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender instanceof Player) return true;
        $item = $sender->getInventory()->getItemInHand();
        if ($item instanceof Pickaxe) {
            $sender->sendMessage(TF::GREEN."Opening Menu...");
            $menu = new CEMenu();
            $menu->openPickaxeMenu($sender);
        } else if ($item instanceof Armor) {
            $sender->sendMessage(TF::GREEN . "Opening Menu...");
            $menu = new CEMenu();
            $menu->openArmorMenu($sender);
        } else if ($item instanceof Sword) {
            $sender->sendMessage(TF::GREEN . "Opening Menu...");
            $menu = new CEMenu();
            $menu->openSwordMenu($sender);
        } else {
            $sender->sendMessage(TF::RED."You must be holding a pickaxe or armor or sword to open this menu!");
        }
        return true;
    }

}