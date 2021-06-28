<?php namespace Taco\ZP\tasks;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\BinaryStream;
use Taco\ZP\Loader;
use Taco\ZP\utils\Utils;
use function arsort;

class ScoreboardTask extends Task {

    private array $line = [];

    public function onRun(int $currentTick) : void {
        foreach(Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if (!isset(Loader::getInstance()->playerData[$player->getName()])) return;
            $data = Loader::getInstance()->playerData[$player->getName()];
            $cbt = false;
            if (Loader::getPVPUtils()->isInCombatTag($player)) {
                $cbt = true;
                $this->removeScoreboard($player);
                $this->showScoreboard($player);
                $this->clearLines($player);
                $this->addLine("§l§b§c                   §b", $player);
                $this->addLine("§l§6Tag-Time", $player);
                $this->addLine(" - " . Loader::getPVPUtils()->getCombatTime($player), $player);
                $this->addLine("§l§gCombo", $player);
                $this->addLine(" - " . Loader::getPVPUtils()->getCombo($player), $player);
                $this->addLine("§0§b§b                   §a", $player);

            }
            if (!$cbt) {
                $money = Loader::getInstance()->economyAPI->myMoney($player);
                $nextRankPrice = Loader::getUtils()->getPriceOfRank(Loader::getUtils()->n2l(Loader::getUtils()->l2n($data["rank"]) + 1), $data["prestige"]);
                if (Loader::getUtils()->l2n($data["rank"]) + 1 > 26) $nextRankPrice = "/prestige";
                $nextRankPercentage = "";
                if ($nextRankPercentage !== "/prestige") {
                    $nextRankPercentage = (string)((float)($money / $nextRankPrice) * 100) . "%";
                    if (((float)($money / $nextRankPrice) * 100) > 100) $nextRankPercentage = "100.00";
                }
                if ($nextRankPercentage == "") {
                    $fullString = "/prestige";
                } else {
                    $n = $nextRankPrice - $money;
                    if ($n < 0) $n = 0;
                    $fullString = "$" . Loader::getUtils()->intToPrefix((int)$n) . " (" . round($nextRankPercentage, 2) . "%)";
                }
                $this->removeScoreboard($player);
                $this->showScoreboard($player);
                $this->clearLines($player);
                $this->addLine("§l§b§c                   §b", $player);
                $this->addLine("§l§aBalance", $player);
                $this->addLine("§f$" . Loader::getUtils()->intToPrefix($money), $player);
                $this->addLine("§a§l§b           ", $player);
                $this->addLine("§l§5Sell Booster", $player);
                $this->addLine("§f" . $data["multiplier"] . "x", $player);
                $this->addLine("§l§o§d      ", $player);
                $this->addLine("§l§4Rank", $player);
                $this->addLine("§l§f" . $data["rank"] . " §r§8[§l§7P" . $data["prestige"] . "§r§8]", $player);
                $this->addLine("§l§o§d§a       ", $player);
                $this->addLine("§l§6Next Rankup", $player);
                $this->addLine("§f" . $fullString, $player);
                $this->addLine("§l§d§b§0     ", $player);
                $this->addLine("§l§3Tokens", $player);
                $this->addLine("§f" . $data["tokens"], $player);
            }

            if (Loader::getEventManager()->time > 1) {
                $timeLeft = Utils::intToString(600 - Loader::getEventManager()->time);
                $pk = new TextPacket();
                $pk->type = TextPacket::TYPE_JUKEBOX_POPUP;
                $pk->message = "§l§dMine§bEvent §r§d$timeLeft";
                $player->dataPacket($pk);
            }
            if (Loader::getEventManager()->kothRunning) {
                $capping = Loader::getEventManager()->capping;
                $cappingTime = Loader::getEventManager()->cappingTime;
                $pk = new TextPacket();
                $pk->type = TextPacket::TYPE_JUKEBOX_POPUP;
                $pk->message = "§l§dK§bo§dT§bH §r§bCapping: §d$capping §r§7| §bTime: §d$cappingTime/100";
                $player->dataPacket($pk);
            }
        }
    }

    public function showScoreboard(Player $player) : void {
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = "sidebar";
        $pk->objectiveName = $player->getName();
        $pk->displayName = "§l§dZ§bPrisons";
        $pk->criteriaName = "dummy";
        $pk->sortOrder = 0;
        $player->sendDataPacket($pk);
    }

    public function addLine(string $line, Player $player) : void {
        $score = count($this->line) + 1;
        $this->setLine($score,$line,$player);
    }

    public function removeScoreboard(Player $player) : void {
        $objectiveName = $player->getName();
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = $objectiveName;
        $player->sendDataPacket($pk);
    }

    public function clearLines(Player $player) {
        for ($line = 0; $line <= 15; $line++) {
            $this->removeLine($line, $player);
        }
    }

    public function setLine(int $loc, string $msg, Player $player) : void {
        $pk = new ScorePacketEntry();
        $pk->objectiveName = $player->getName();
        $pk->type = $pk::TYPE_FAKE_PLAYER;
        $pk->customName = $msg;
        $pk->score = $loc;
        $pk->scoreboardId = $loc;
        if (isset($this->line[$loc])) {
            unset($this->line[$loc]);
            $pkt = new SetScorePacket();
            $pkt->type = $pkt::TYPE_REMOVE;
            $pkt->entries[] = $pk;
            $player->sendDataPacket($pkt);
        }
        $pkt = new SetScorePacket();
        $pkt->type = $pkt::TYPE_CHANGE;
        $pkt->entries[] = $pk;
        $player->sendDataPacket($pkt);
        $this->line[$loc] = $msg;
    }

    public function removeLine(int $line, Player $player) : void {
        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_REMOVE;
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $player->getName();
        $entry->score = $line;
        $entry->scoreboardId = $line;
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);
        if (isset($this->line[$line])) {
            unset($this->line[$line]);
        }
    }

}