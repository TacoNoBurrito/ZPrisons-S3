<?php namespace Taco\ZP\tasks\async;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use Taco\ZP\Loader;

class LoadPlayer extends AsyncTask {

    private int $kills = 0;

    private int $deaths = 0;

    private int $killstreak = 0;

    private string $rank = "A";

    private int $prestige = 0;

    private int $tokens = 0;

    private string $player = "";

    private string $multiplier = "1.0";

    private string $dataFolder = "";

    private string $tag = "";

    private string $gang = "";

    private bool $isMuted = false;

    private string $muteReason = "";

    private bool $isBanned = false;

    private string $banReason = "";

    public function __construct(string $player, string $dataFolder) {
        $this->player = trim($player);
        $this->dataFolder = $dataFolder;
    }

    public function onRun() : void {
        $name = trim($this->player);
        $db = new \SQLite3($this->dataFolder."database.db");
        $query = $db->query("SELECT * FROM users WHERE username = '$name';")->fetchArray(SQLITE3_ASSOC);
        if (empty($query)) return;
        $this->kills = $query["kills"];
        $this->deaths = $query["deaths"];
        $this->killstreak = $query["killstreak"];
        $this->rank = $query["prank"];
        $this->prestige = $query["prestige"];
        $this->tokens = $query["tokens"];
        $this->multiplier = $query["multiplier"];
        $this->tag = $query["tag"];
        $this->gang = $query["gang"];
        $query = $db->query("SELECT * FROM punishments WHERE username = '$name';")->fetchArray(SQLITE3_ASSOC);
        if (empty($query)) return;
        $this->isMuted = !($query["isMuted"] == 0);
        $this->muteReason = $query["muteReason"];
        $this->isBanned = !($query["isBanned"] == 0);
        $this->banReason = $query["banReason"];
        $db->close();
    }

    public function onCompletion(Server $server) : void {
        if ($this->isBanned) {
            $server->getPlayer($this->player)->kick("You are banned!\nReason: ".$this->banReason, false);
            return;
        }
        Loader::getInstance()->punishmentData[$this->player] = [
            "isMuted" => $this->isMuted,
            "muteReason" => $this->muteReason,
            "isBanned" => $this->isBanned,
            "banReason" => $this->banReason
        ];
        if (Loader::getInstance()->votePointDB->exists($this->player)) {
            $vp = Loader::getInstance()->votePointDB->get($this->player);
        } else {
            $vp = 0;
        }
        if (Loader::getInstance()->blocksBroken->exists($this->player)) {
            $bb = Loader::getInstance()->blocksBroken->get($this->player);
        } else {
            $bb = 0;
        }
        Loader::getInstance()->playerData[$this->player] = [
            "kills" => $this->kills,
            "deaths" => $this->deaths,
            "killstreak" => $this->killstreak,
            "rank" => $this->rank,
            "prestige" => $this->prestige,
            "tokens" => $this->tokens,
            "multiplier" => $this->multiplier,
            "tag" => $this->tag,
            "gang" => $this->gang,
            "vp" => $vp,
            "blocksBroken" => $bb
        ];
    }

}