<?php

namespace Albert3643;

use Albert3643\utils\Utils;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\item\ItemTypeIds;

class Entity extends Human
{
  public bool $canCollide = false;
  protected bool $immobile = true;
  public static function create(Player $player):self
  {
    $nbt = CompoundTag::create()->setTag("Pos", new ListTag([
      new DoubleTag($player->getLocation()->x),
      new DoubleTag($player->getLocation()->y),
      new DoubleTag($player->getLocation()->z)
    ]))->setTag("Motion", new ListTag([
      new DoubleTag($player->getMotion()->x),
      new DoubleTag($player->getMotion()->y),
      new DoubleTag($player->getMotion()->z)
    ]))->setTag("Rotation", new ListTag([
      new FloatTag($player->getLocation()->yaw),
      new FloatTag($player->getLocation()->pitch)
    ]));
    return new self($player->getLocation(), $player->getSkin(), $nbt);
  }

  protected function getInitialDragMultiplier():float
  {
    return 0.00;
  }
  
  protected function getInitialGravity():float
  {
    return 0.00;
  }
 
  public function onUpdate(int $currentTick):bool
  {   
    $this->setNameTagAlwaysVisible();
    $this->setNameTag("§r§l§b« Daily »
    Use §3« /daily »");
    return parent::onUpdate($currentTick);
  }

  public function attack(EntityDamageEvent $source):void
  {
    $source->cancel();
    if($source instanceof EntityDamageByEntityEvent)
    {
      $damager = $source->getDamager();
      if($damager instanceof Player)
      {
      Utils::dailyMenu($damager);
      }
    }
  }
}