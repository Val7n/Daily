<?php

namespace Albert3643;

use pocketmine\utils\SingletonTrait;
use pocketmine\plugin\PluginBase;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\world\World;
use muqsit\invmenu\InvMenuHandler;
use Albert3643\provider\Dailys;
use Albert3643\Entity;
use Albert3643\utils\DailyRewardTask;
use Albert3643\provider\DailyRewards;
use Albert3643\commands\DailyCommand;
use Albert3643\commands\EditSubCommand;
use Albert3643\commands\EditMenuSubCommand;
use Albert3643\commands\SpawnSubCommand;

class Main extends PluginBase
{
  use SingletonTrait;
  public function onLoad():void
  {
    self::setInstance($this);
  }
  
  public function onEnable():void
  {
    if(!InvMenuHandler::isRegistered())
    {
      InvMenuHandler::register($this);
    }
    $this->getScheduler()->scheduleRepeatingTask(new DailyRewardTask(new DailyRewards()), 20);
    $this->getServer()->getCommandMap()->register("", new DailyCommand());
    $this->getServer()->getCommandMap()->register("", new EditSubCommand());
    $this->getServer()->getCommandMap()->register("", new SpawnSubCommand());
    $this->getServer()->getCommandMap()->register("", new EditMenuSubCommand());
    $this->getServer()->getCommandMap()->register("", new ResetSubCommand());
    $this->getServer()->getCommandMap()->register("", new GiveSubCommand());
    $dailys = [1,2,3,4,5,6,7];
    foreach($dailys as $daily)
    {
      Dailys::getInstance()->setDaily($daily);
    }
    EntityFactory::getInstance()->register(Entity::class,function (World $world, CompoundTag $nbt):Entity
    {
      return new Entity(EntityDataHelper::parseLocation($nbt,$world),Entity::parseSkinNBT($nbt), $nbt);
    },['Entity']);
}

    public function checkAndClaimDaily(Player $player) {
    $lastClaimTime = $this->getLastDailyClaimTime($player);
    $currentTime = time();
    $cooldown = 24 * 60 * 60;

    if ($currentTime - $lastClaimTime >= $cooldown) {
        $player->sendMessage(TextFormat::GREEN . "Has reclamado tu recompensa diaria");
        
        $this->setLastDailyClaimTime($player, $currentTime);
    } else {
        $remainingTime = $cooldown - ($currentTime - $lastClaimTime);
        $hours = floor($remainingTime / 3600);
        $minutes = floor(($remainingTime % 3600) / 60);

        $message = TextFormat::YELLOW . "Te quedan ";
        if ($hours > 0) {
            $message .= "$hours horas y ";
        }
        $message .= "$minutes minutos para reclamar el siguiente Daily.";
        
        $player->sendMessage($message);
        }
    }
}