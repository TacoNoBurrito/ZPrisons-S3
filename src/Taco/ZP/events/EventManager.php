<?php namespace Taco\ZP\events;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use Taco\ZP\events\types\kothevent\KothEventTask;
use Taco\ZP\events\types\miningevent\MiningEventTask;
use Taco\ZP\Loader;
use Taco\ZP\utils\Utils;
use function count;

class EventManager {

    /// MINE EVENT
    ///
    public int $time = 0;
    public array $playing = [];
    private int $prize = 0;
    ///
    /// MINE EVENT

    /// KOTH EVENT
    ///
    public string $capping = "";
    public int $cappingTime = 0;
    public bool $kothRunning = false;
    ///
    /// KOTH EVENT

    public function startEvent(string $event, int $prize) : void {
        if ($event == "mine") {
            Loader::getInstance()->getServer()->broadcastMessage("§7[§r§l§dZ§bPrisons§r§7] §fA Mine-Event has started! Mine the most blocks before the time runs out to win §a$".$prize."!");
            foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
                $this->playing[$player->getName()] = 0;
                $player->sendTitle("§l§dMine§bEvent!", "§r§7Mine the most blocks to win!");
            }
            $this->prize = $prize;
            Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new MiningEventTask(), 20);
            return;
        }
        if ($event == "koth") {
            Loader::getInstance()->getServer()->broadcastMessage("§7[§r§l§dZ§bPrisons§r§7] §fA KoTH event has started! Goto the pvpmine and stay in the basketball court for 100 seconds without dying to cap and win an §r§7[§r§dOMEGA§6AXE§r§7]§f!");
            Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new KothEventTask(), 20);
            $this->kothRunning = true;
        }
    }

    public function endMiningEvent() : void {
        if (count($this->playing) < 1) return;
        arsort($this->playing);
        foreach($this->playing as $name => $mined) {
            Loader::getInstance()->getServer()->broadcastMessage("§l§dMine§bEvent! §r§fThe Game Has Ended!\n§bWinner: §d".$name."\n§bWinner-Blocks-Mined: §d".Loader::getUtils()->intToPrefix($mined)."\n§bPrize: §a$".$this->prize."\n§bGood Game!");
            $player = Loader::getInstance()->getServer()->getPlayer($name);
            Loader::getInstance()->economyAPI->addMoney($player, $this->prize);
            break;
        }
        $this->time = 0;
        $this->playing = [];
        $this->prize = 0;
    }

    public function endKothEvent() : void {
        Loader::getInstance()->getServer()->broadcastMessage("§l§dK§bo§dT§bH §r§fThe player ".$this->capping." has won the §l§dK§bo§dT§bH §r§fevent!");
        $this->cappingTime = 0;
        $this->kothRunning = false;
        $omegaAXE = new Item(ItemIds::DIAMOND_AXE);
        $omegaAXE->setCustomName("§r§7[§r§dOMEGA§6AXE§r§7]");
        $enchINST = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 7);
        $omegaAXE->addEnchantment($enchINST);
        $enchINST = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::FIRE_ASPECT), 2);
        $omegaAXE->addEnchantment($enchINST);
        $player = Loader::getInstance()->getServer()->getPlayer($this->capping);
        if ($player !== null) {
            if ($player->getInventory()->canAddItem($omegaAXE)) {
                $player->getInventory()->addItem($omegaAXE);
            } else {
                $player->sendMessage("§cYour inventory does not have space for the omega axe so you have dropped it.");
                $player->dropItem($omegaAXE);
            }
        }
        $this->capping = "";
    }

}