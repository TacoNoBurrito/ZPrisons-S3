<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class ShopCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("shop", $owner);
        $this->setDescription("Open the shop.");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender instanceof Player) return true;
        Loader::getForms()->openShopForm1($sender);
        return true;
    }

}