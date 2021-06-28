<?php namespace Taco\ZP;

use falkirks\minereset\Mine;
use falkirks\minereset\MineReset;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\block\BlockFactory;
use pocketmine\entity\Entity;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\ce\CEManager;
use Taco\ZP\commands\BanCommand;
use Taco\ZP\commands\BuilderModeCommand;
use Taco\ZP\commands\CECommand;
use Taco\ZP\commands\FixCommand;
use Taco\ZP\commands\GangCommand;
use Taco\ZP\commands\KickCommand;
use Taco\ZP\commands\MinesCommand;
use Taco\ZP\commands\MiningBoosterCommand;
use Taco\ZP\commands\MuteCommand;
use Taco\ZP\commands\NightVisionCommand;
use Taco\ZP\commands\OpCommand;
use Taco\ZP\commands\PardonCommand;
use Taco\ZP\commands\PickaxeCommand;
use Taco\ZP\commands\PrestigeCommand;
use Taco\ZP\commands\RankupCommand;
use Taco\ZP\commands\ReportCommand;
use Taco\ZP\commands\SellCommand;
use Taco\ZP\commands\ShopCommand;
use Taco\ZP\commands\SpawnCommand;
use Taco\ZP\commands\TagsCommand;
use Taco\ZP\commands\UpgradeMenuCommand;
use Taco\ZP\commands\VanishCommand;
use Taco\ZP\commands\VotePointsCommand;
use Taco\ZP\commands\WarpCommand;
use Taco\ZP\commands\WithdrawCommand;
use Taco\ZP\crates\CratesFireworkEntity;
use Taco\ZP\crates\CratesManager;
use Taco\ZP\events\EventManager;
use Taco\ZP\farm\entities\CowEntity;
use Taco\ZP\farm\entities\PigEntity;
use Taco\ZP\farm\FarmManager;
use Taco\ZP\ft\FloatingTextEntity;
use Taco\ZP\ft\FloatingTextUtils;
use Taco\ZP\leaderboards\LeaderboardEntity;
use Taco\ZP\leaderboards\LeaderboardUtils;
use Taco\ZP\listeners\EventListener;
use Taco\ZP\modules\chatGames\ChatGames;
use Taco\ZP\modules\quests\Quests;
use Taco\ZP\npc\NPCEntity;
use Taco\ZP\npc\NPCUtils;
use Taco\ZP\randomEntities\DamageEntity;
use Taco\ZP\tasks\AnnouncementTask;
use Taco\ZP\tasks\async\SavePlayer;
use Taco\ZP\tasks\AutoSaveTask;
use Taco\ZP\tasks\CustomEnchantTask;
use Taco\ZP\tasks\EntityClearTask;
use Taco\ZP\tasks\NametagTask;
use Taco\ZP\tasks\ScoreboardTask;
use Taco\ZP\tasks\ScoreTagTask;
use Taco\ZP\tasks\TimeTask;
use Taco\ZP\useful\Forms;
use Taco\ZP\useful\generator\VoidGenerator;
use Taco\ZP\useful\InvMenus;
use Taco\ZP\utils\ACUtils;
use Taco\ZP\utils\AreaUtils;
use Taco\ZP\utils\DrugItems;
use Taco\ZP\utils\GangUtils;
use Taco\ZP\utils\PVPUtils;
use Taco\ZP\utils\Utils;
use Taco\ZP\utils\VoucherUtils;
use Taco\ZP\vanillaShake\block\Hopper;
use Taco\ZP\vanillaShake\tile\Tile;

class Loader extends PluginBase {

    protected static Loader $instance;

    protected static DrugItems $drugItems;

    protected static NPCUtils $NPCUtils;

    protected static AreaUtils $areaUtils;

    protected static Utils $utils;

    protected static ACUtils $acutils;

    protected static ChatGames $chatGames;

    protected static Forms $forms;

    protected static PVPUtils $PVPUtils;

    protected static InvMenus $invMenus;

    protected static VoucherUtils $voucherUtils;

    protected static GangUtils  $gangUtils;

    protected static Quests $quests;

    protected static FarmManager $farmManager;

    protected static CratesManager $cratesManager;

    protected static EventManager $eventManager;

    public $economyAPI;

    public $purePerms;

    public $mineReset;

    public Config $areaDB;

    public Config $gangDB;

    public Config $votePointDB;

    public Config $blocksBroken;

    public array $areas = [];

    public array $gangs = [];

    public array $playerData = [];

    public array $gangInvites = [];

    public array $punishmentData = [];

    public array $builderMode = [];

    public const WORLD_PLOTS = "plots";
    public const WORLD_SPAWN = "spawn";
    public const WORLD_PVP = "pvp";

    public bool $globalMute = false;

    public const WEBHOOK_REPORT = "https://canary.discord.com/api/webhooks/858099062921101362/9-efdryDseRq-uq0664BvMQe9dxIxCSWKFBMGisD1ZRJqm3RP6Me_wi0uRCSszdrut7r";

    public const CANNOT_DO_THAT_HERE = TF::BOLD.TF::RED."Hey! ".TF::RESET.TF::GRAY."Sorry, but you cannot do that here!";

    //https://github.com/pmmp/PocketMine-MP/blob/stable/src/pocketmine/block/BlockIds.php
    public const SELL_PRICES = [
        "1:0" => 0.3,
        "15:0" => 0.7,
        "16:0" => 0.5,
        "4:0" => 0.3,
        "263:0" => 0.5,
        "265:0" => 6,
        "388:0" => 2,
        "266:0" => 3,
        "14:0" => 4,
        "57:0" => 10,
        "41:0" => 8,
        "48:0" => 1,
        "56" => 12,
		"49:0" => 35,
        "133:0" => 40,
        "264:0" => 5,
        "81:0" => 12000
    ];

    public function onEnable() : void {
        GeneratorManager::addGenerator(VoidGenerator::class, "void", true);
        self::$instance = $this;
        self::$PVPUtils = new PVPUtils();
        self::$eventManager = new EventManager();
        self::$drugItems = new DrugItems();
        self::$gangUtils = new GangUtils();
        self::$invMenus = new InvMenus();
        self::$voucherUtils = new VoucherUtils();
        self::$utils = new Utils();
        self::$areaUtils = new AreaUtils();
        self::$quests = new Quests();
        self::$acutils = new ACUtils();
        self::$cratesManager = new CratesManager();
        self::$chatGames = new ChatGames();
        self::$chatGames->startGames();
        self::$forms = new Forms();
        CEManager::init();
        $this->unregisterCommands(["kill", "me"]);
        $this->areaDB = new Config($this->getDataFolder()."areas.yml", Config::YAML);
        $areas = (array)$this->areaDB->getAll();
        $this->areas = $areas;
        $this->gangDB = new Config($this->getDataFolder()."gangs.yml", Config::YAML);
        $this->blocksBroken = new Config($this->getDataFolder()."blocks.yml", Config::YAML);
        $this->gangs = (array)$this->gangDB->getAll();
        $this->votePointDB = new Config($this->getDataFolder()."vp.yml", Config::YAML);
        foreach (array_diff(scandir($this->getServer()->getDataPath() . "worlds"), ["..", "."]) as $levelName) {
            $this->getServer()->loadLevel($levelName);
        }
        $this->registerListeners();
        $this->registerDatabase();
        $this->getServer()->getNetwork()->setName("§r§l§dZ§bPrisons §r§f(S3)§7");
        $this->registerExternalPlugins();
        $this->registerCommands();
        $this->registerTasks();
        Entity::registerEntity(NPCEntity::class);
        Entity::registerEntity(FloatingTextEntity::class);
        Entity::registerEntity(DamageEntity::class);
        Entity::registerEntity(CowEntity::class);
        Entity::registerEntity(PigEntity::class);
        Entity::registerEntity(CratesFireworkEntity::class);
        Entity::registerEntity(LeaderboardEntity::class);
        if (!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
        $this->getServer()->setDefaultLevel($this->getServer()->getLevelByName("spawn"));
        $ftU = new FloatingTextUtils();
        $ftU->register();
        self::$NPCUtils = new NPCUtils();
        self::$NPCUtils->loadNPCs();
        self::$farmManager = new FarmManager();
        self::$farmManager->init();
        $e = new LeaderboardUtils();
        $e->registerLeaderboards();
        $this->registerBlockStuff();
    }

    public function registerBlockStuff() : void {
        Tile::init();
        BlockFactory::registerBlock(new Hopper(), true);
    }

    public function onDisable() : void {
        foreach($this->gangs as $name => $info) {
            $this->gangDB->setNested($name.".kills", $info["kills"]);
            $this->gangDB->setNested($name.".deaths", $info["deaths"]);
            $this->gangDB->setNested($name.".members", $info["members"]);
            $this->gangDB->setNested($name.".leader", $info["leader"]);
        }
        $this->gangDB->save();
    }

    public function registerTasks() : void {
        $this->getScheduler()->scheduleRepeatingTask(new NametagTask(), 20 * 60);
        $this->getScheduler()->scheduleRepeatingTask(new AnnouncementTask(), 20 * 120);
        $this->getScheduler()->scheduleRepeatingTask(new ScoreboardTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new EntityClearTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new TimeTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new CustomEnchantTask(), 20);
        $this->getScheduler()->scheduleRepeatingTask(new ScoreTagTask(), 20 * 25);
        $this->getScheduler()->scheduleRepeatingTask(new AutoSaveTask(),  900 * 20);
    }

    public function registerCommands() : void {
        $this->getServer()->getCommandMap()->registerAll("ZPrisons", [
            new OpCommand($this),
            new RankupCommand($this),
            new PrestigeCommand($this),
            new WarpCommand($this),
            new FixCommand($this),
            new MinesCommand($this),
            new WithdrawCommand($this),
            new TagsCommand($this),
            new GangCommand($this),
            new SpawnCommand($this),
            new SellCommand($this),
            new MiningBoosterCommand($this),
            new UpgradeMenuCommand($this),
            new CECommand($this),
            new NightVisionCommand($this),
            new PickaxeCommand($this),
            new BanCommand($this),
            new MuteCommand($this),
            new KickCommand($this),
            new PardonCommand($this),
            new ReportCommand($this),
            new VotePointsCommand($this),
            new VanishCommand($this),
            new BuilderModeCommand($this),
            new ShopCommand($this)
        ]);
    }

    public function registerDatabase() : void
    {
        $db = new \SQLite3($this->getDataFolder() . "database.db");
        $db->query("CREATE TABLE IF NOT EXISTS users(username TEXT PRIMARY KEY COLLATE NOCASE, gang TEXT, tag TEXT, kills INT, deaths INT, killstreak INT, prank TEXT, prestige INT, tokens INT, multiplier TEXT);");
        $db->query("CREATE TABLE IF NOT EXISTS punishments(username TEXT PRIMARY KEY COLLATE NOCASE, isMuted INT, muteReason TEXT, isBanned INT, banReason TEXT);");
        $db->close();
    }

    public function unregisterCommands(array $commands)  : void {
        foreach ($commands as $cmd) {
            $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand($cmd));
        }
    }

    public function registerExternalPlugins() : void {
        $this->economyAPI = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $this->purePerms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
        $this->mineReset = $this->getServer()->getPluginManager()->getPlugin("MineReset");
    }

    public function registerListeners() : void {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        //a real sussy baka $this->getServer()->getPluginManager()->registerEvents(new AntiCheat(), $this);
    }

    public static function getInstance() : self {
        return self::$instance;
    }

    public static function getDrugItems() : DrugItems {
        return self::$drugItems;
    }

    public static function getUtils() : Utils {
        return self::$utils;
    }

    public static function getAreaUtils() : AreaUtils {
        return self::$areaUtils;
    }

    public static function getACUtils() : ACUtils {
        return self::$acutils;
    }

    public static function getNPCUtils() : NPCUtils {
        return self::$NPCUtils;
    }

    public static function getChatGames() : ChatGames {
        return self::$chatGames;
    }

    public static function getForms() : Forms {
        return self::$forms;
    }

    public static function getPVPUtils() : PVPUtils {
        return self::$PVPUtils;
    }

    public static function getInvMenus() : InvMenus {
        return self::$invMenus;
    }

    public static function getVoucherUtils() : VoucherUtils {
        return self::$voucherUtils;
    }

    public static function getGangUtils() : GangUtils {
        return self::$gangUtils;
    }

    public static function getFarmUtils() : FarmManager {
        return self::$farmManager;
    }

    public static function getCratesManager() : CratesManager {
        return self::$cratesManager;
    }

    public static function getEventManager() : EventManager {
        return self::$eventManager;
    }

}