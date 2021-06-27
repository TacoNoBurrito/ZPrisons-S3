<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class WithdrawCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("withdraw", $owner);
        $this->setDescription("Withdraw money into a note!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if ($sender instanceof Player) {
            $amount = $args[0];
            if (empty($args[0])) {
                $sender->sendMessage(TF::RED."Please provide an amount to withdraw!");
                return true;
            }
            if (!is_numeric($amount)) {
                $sender->sendMessage(TF::RED."The amount must be a number!");
                return true;
            }
            if ($amount < 1000) {
                $sender->sendMessage(TF::RED."You must withdraw AT-LEAST $1000.");
                return true;
            }
            if ($amount >= Loader::getInstance()->economyAPI->myMoney($sender)) {
                $sender->sendMessage(TF::RED."You do not have enough money to withdraw this amount.");
                return true;
            }
            Loader::getVoucherUtils()->giveMoneyVoucher($sender, $amount);
            $sender->sendMessage(TF::GREEN."Successfully redeemed voucher.");
            Loader::getInstance()->economyAPI->reduceMoney($sender, $amount);
        }
        return true;
    }

}