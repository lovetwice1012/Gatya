<?php
namespace Deceitya\Gatya\Command;

use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use Deceitya\Gatya\Main;
use Deceitya\Gatya\Form\SeriesForm;
use Deceitya\Gatya\Series\SeriesFactory;
use Deceitya\Gatya\Utils\MessageContainer;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;

class GatyaCommand extends Command
{
	/** @var Main $plugin */
	public $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct('gt', MessageContainer::get('command.gt.description'), MessageContainer::get('command.gt.usage'));

        $this->setPermission('gatya.command.gt');
	    $this->setPlugin($plugin);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
//        if (!parent::execute($sender, $commandLabel, $args)) {
//            return false;
//        }

	    if(!$this->testPermission($sender)){
		    return true;
	    }

        if (!($sender instanceof Player)) {
            return true;
        }

        if (count($args) < 1) {
            $sender->sendForm(new SeriesForm());

            return true;
        }

        try {
            $series = SeriesFactory::getSeries(array_shift($args));
            $count = array_shift($args) ?? 1;
            $api = EconomyAPI::getInstance();

            for ($i = 0; $i < $count; $i++) {
                if ($api->myMoney($sender) < $series->getCost()) {
                    $sender->sendMessage(MessageContainer::get('command.gt.no_money'));

                    return true;
                }

                $item = $series->getItem(mt_rand(0, 10000) / 100);
                if (empty($sender->getInventory()->addItem($item))) {
                    $sender->sendMessage(MessageContainer::get('command.gt.result', $item->getCustomName() ?: $item->getName()));
                    $api->reduceMoney($sender, $series->getCost());
                } else {
                    $sender->sendMessage(MessageContainer::get('command.gt.no_space'));

                    return true;
                }
            }

            return true;
        } catch (\Exception $e) {
            $sender->sendMessage(MessageContainer::get('command.gt.no_series'));

            return true;
        }
    }

	public function getPlugin() : Main{
		return $this->plugin;
	}

	protected function setPlugin(Main $plugin) : void{
		$this->plugin = $plugin;
	}
}
