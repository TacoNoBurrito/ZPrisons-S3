<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use Taco\ZP\Loader;

class MinesCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("mines", $owner);
        $this->setDescription("Warp to a unlocked mine!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        Loader::getForms()->openMinesForm($sender);
        return true;
    }

}