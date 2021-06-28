<?php namespace Taco\ZP\tasks\async;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use Taco\ZP\Loader;

class SavePlayer extends AsyncTask {

    private int $kills = 0;

    private int $deaths = 0;

    private int $killstreak = 0;

    private string $rank = "";

    private int $prestige = 0;

    private int $tokens = 0;

    private string $multiplier = "";

    private string $player = "";

    private string $dataFolder = "";

    private string $tag = "";

    private string $gang = "";

    private bool $isMuted = false;

    private string $muteReason = "";

    private bool $isBanned = false;

    private string $banReason = "";

    private int $vp = 0;

    private int $blocksBroken = 0;

    public function __construct(string $player, int $kills, int $deaths, int $killstreak, string $rank, int $prestige, int $tokens, string $multiplier, string $dataFolder, string $tag, string $gang, bool $isMuted, string $muteReason, bool $isBanned, string $banReason, int $vp, int $bb) {
        $this->player = $player;
        $this->kills = $kills;
        $this->deaths = $deaths;
        $this->killstreak = $killstreak;
        $this->rank = $rank;
        $this->prestige= $prestige;
        $this->tokens = $tokens;
        $this->multiplier = (string)$multiplier;
        $this->dataFolder = $dataFolder;
        $this->tag = $tag;
        $this->gang = $gang;
        $this->isMuted = $isMuted;
        $this->isBanned = $isBanned;
        $this->muteReason = $muteReason;
        $this->banReason = $banReason;
        $this->vp = $vp;
        $this->blocksBroken = $bb;
    }

    public function onRun() : void {
        mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
        $name = (string)trim($this->player);
        $db = new \SQLite3($this->dataFolder."database.db");
        $kills = (int)$this->kills;
        $deaths = (int)$this->deaths;
        $killstreak = (int)$this->killstreak;
        $rank = (string)$this->rank;
        $prestige = (int)$this->prestige;
        $tokens = (int)$this->tokens;
        $multiplier = (string)$this->multiplier;
        $tag = $this->tag;
        $gang = $this->gang;
        $insert = $db->prepare("INSERT OR REPLACE INTO users(username, gang, tag, prestige, kills, deaths, killstreak, prank, tokens, multiplier) VALUES (:username, :gang, :tag, :prestige, :kills, :deaths, :killstreak, :prank, :tokens, :multiplier)");
        $insert->bindValue(":username", $name);
        $insert->bindValue(":gang", $gang);
        $insert->bindValue(":tag", $tag);
        $insert->bindValue(":prestige", $prestige);
        $insert->bindValue(":kills", $kills);
        $insert->bindValue(":deaths", $deaths);
        $insert->bindValue(":prestige", $prestige);
        $insert->bindValue(":killstreak", $killstreak);
        $insert->bindValue(":prank", $rank);
        $insert->bindValue(":tokens", $tokens);
        $insert->bindValue(":multiplier", $multiplier);
        $insert->execute();
        $isMuted = $this->isMuted;
        $muteReason = $this->muteReason;
        $isBanned = $this->isBanned;
        $banReason = $this->banReason;
        $insert = $db->prepare("INSERT OR REPLACE INTO punishments(username, isMuted, muteReason, isBanned, banReason) VALUES (:username, :isMuted, :muteReason, :isBanned, :banReason)");
        $insert->bindValue(":username", $name);
        $insert->bindValue(":isMuted", $isMuted);
        $insert->bindValue(":muteReason", $muteReason);
        $insert->bindValue(":isBanned", $isBanned);
        $insert->bindValue(":banReason", $banReason);
        $insert->execute();
        $db->close();
    }

    public function onCompletion(Server $server)
    {
        Loader::getInstance()->votePointDB->set($this->player, $this->vp);
        Loader::getInstance()->votePointDB->save();
        Loader::getInstance()->blocksBroken->set($this->player, $this->blocksBroken);
        Loader::getInstance()->blocksBroken->save();
    }

}