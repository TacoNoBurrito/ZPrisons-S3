<?php namespace Taco\ZP\npc;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class NPCUtils {

    public array $npcs = [
        TF::BOLD.TF::GOLD."Blackmarket".TF::EOL.TF::RESET.TF::GRAY.TF::ITALIC."Tap me to open the blackmarket!" => [
            "is-Console" => true,
            "command" => "opc sendbm {player}",
            "pos" => "291:67:235"
        ]
    ];

    public function spawnNPC(string $name, string $command, Vector3 $pos, bool $isConsoleCommand) : void {
        Loader::getInstance()->getServer()->getDefaultLevel()->loadChunk($pos->getX() >> 16, $pos->getZ() >> 16);
        $nbt = Entity::createBaseNBT($pos, null, 0, 0);
        $entity = new NPCEntity(Loader::getInstance()->getServer()->getDefaultLevel(), $nbt, $name, $command, $isConsoleCommand);
        $entity->spawnToAll();
    }

    public function loadNPCs() : void {
        foreach($this->npcs as $name => $info) {
            $pos = Loader::getAreaUtils()->formattedToVec3($info["pos"]);
            $this->spawnNPC($name, $info["command"], $pos, $info["is-Console"]);

        }
    }

}