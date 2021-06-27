<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use Taco\ZP\Loader;

class VanishCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("v", $owner);
        $this->setDescription("Vanish from existence.");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender instanceof Player) return true;
        if ($sender->hasPermission("core.vanish")) {
            if ($sender->isInvisible()) {
                $sender->sendMessage("§aYou are no longer vanished.");
                $sender->setInvisible(false);
            } else {
                $sender->sendMessage("§aYou are now vanished.");
                $sender->setInvisible(true);
            }
        }
        return true;
    }

}