<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\ce\CEManager;
use Taco\ZP\Loader;

class CECommand extends PluginCommand {

    //The base of this file was created by prim69 and boomyourbang. Re-done for ZPrisons Core

    public const RARITY_COLORS = [
        Enchantment::RARITY_COMMON => TF::YELLOW,
        Enchantment::RARITY_UNCOMMON => TF::DARK_GREEN,
        Enchantment::RARITY_RARE => TF::BLUE,
        Enchantment::RARITY_MYTHIC => TF::DARK_PURPLE
    ];

    private Loader $plugin;

    public function __construct(Loader $plugin) {
        parent::__construct("ce", $plugin);
        $this->setDescription("[admin]");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if ($sender instanceof Player) return;
            $player = $sender->getServer()->getPlayer($args[1]);
            if($player === null) return;
            $item = $player->getInventory()->getItemInHand();
            if ($item->isNull()) return;
            $enchantment = 70;
            if (is_numeric($args[2])) $enchantment = Enchantment::getEnchantment((int)$args[2]);
            else if (isset(CEManager::CONVERSIONS[strtolower($args[2])])) $enchantment = Enchantment::getEnchantment(CEManager::CONVERSIONS[strtolower($args[2])]);
            else return;
            if (!($enchantment instanceof Enchantment) && is_numeric($args[2])) {
                $sender->sendMessage(TF::RED . $args[2] . " is not a valid enchant!");
                return;
            }
            $level = 1;
            if (isset($args[3])) $level = (int)$args[3];
            $item->addEnchantment(new EnchantmentInstance($enchantment, $level));
            $lores = [];
            $enchants = array_filter($item->getEnchantments(), function ($enchantment) {
                return $enchantment->getId() > 36;
            });

            foreach ($enchants as $enchantment) {
                $lores[] = TF::RESET . self::RARITY_COLORS[$enchantment->getType()->getRarity()] . $enchantment->getType()->getName() . " " . self::lvlToRomanNum($enchantment->getLevel());
            }
            $item->setLore($lores);
            if ($item->getNamedTag()->hasTag("bb")) {
                $l = $item->getLore();
                $newLore = [];
                foreach ($l as $nw) {
                    if (strpos($nw, "Blocks")) {
                        continue;
                    }
                    $newLore[] = $nw;
                }
                $newLore[] = "§r§bBlocks Broken: §f" . $item->getNamedTag()->getInt("bb");
                $item->setLore($newLore);
            }
            $player->getInventory()->setItemInHand($item);
    }

    public static function lvlToRomanNum(int $level) : string{
        $romanNumeralConversionTable = [
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1
        ];
        $romanString = "";
        while($level > 0){
            foreach($romanNumeralConversionTable as $rom => $arb){
                if($level >= $arb){
                    $level -= $arb;
                    $romanString .= $rom;
                    break;
                }
            }
        }
        return $romanString;
    }

}