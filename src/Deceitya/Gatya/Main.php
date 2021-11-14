<?php
namespace Deceitya\Gatya;

use pocketmine\permission\PermissionParser;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use Deceitya\Gatya\Command\GatyaCommand;
use Deceitya\Gatya\Series\SeriesFactory;
use Deceitya\Gatya\Utils\MessageContainer;

class Main extends PluginBase
{
    public function onEnable(): void
    {
        $this->saveResource('series.json');
        MessageContainer::load($this);
        SeriesFactory::init("{$this->getDataFolder()}series.json");
        $this->checkChance();
        $this->registerCommand();
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        return true;
    }

    private function registerCommand()
    {
        $this->getServer()->getCommandMap()->register('gatya', new GatyaCommand($this));
    }

    private function checkChance()
    {
        foreach (SeriesFactory::getAllSeries() as $series) {
            if ($series->getChanceSum() !== 100.0) {
                $this->getLogger()->warning(MessageContainer::get('chance_invalid', $series->getName()));
                $this->getServer()->getPluginManager()->disablePlugin($this);
            }
        }
    }
}
