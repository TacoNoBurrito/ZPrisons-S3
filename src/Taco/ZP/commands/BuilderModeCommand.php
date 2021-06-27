<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use Taco\ZP\Loader;
use function in_array;


class BuilderModeCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("bm", $owner);
        $this->setDescription("Enter builder mode.");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if ($sender instanceof Player) {
            if($sender->hasPermission("core.bm")) {
                if (in_array($sender->getName(), Loader::getInstance()->builderMode)) {
                    $sender->sendMessage("§aYou have left builder mode.");
                    $sender->setGamemode(0);
                    unset(Loader::getInstance()->builderMode[$sender->getName()]);
                } else {
                    $sender->sendMessage("§aYou are now in builder mode.");
                    Loader::getInstance()->builderMode[] = $sender->getName();
                    $sender->setGamemode(1);
                }
            }
        }
        return true;
    }

}