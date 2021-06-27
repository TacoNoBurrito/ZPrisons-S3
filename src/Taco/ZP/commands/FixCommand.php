<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use Taco\ZP\Loader;

class FixCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("fix", $owner);
        $this->setDescription("Fix your items and/or armor.");
        $this->setAliases(["repair"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if ($sender instanceof Player) {
            //if ($sender->hasPermission("core.fix")) {
                Loader::getForms()->openRepairForm($sender);
            //} else {
              //  $sender->sendMessage(TF::RED."You do not have permission to use this command! ".TF::RESET.TF::BOLD.TF::GRAY."COAL ".TF::RESET.TF::RED."and above can use this command!");
            //}
        }
        return true;
    }

}