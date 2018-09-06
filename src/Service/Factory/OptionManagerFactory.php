<?php
namespace XT\Option\Service\Factory;


use XT\Option\Service\Groups;
use XT\Option\Service\OptionManager;
use XT\Option\Service\Options;

class OptionManagerFactory
{
    public function __invoke($sm)
    {
        if (!file_exists(OptionManager::$fileconfig))
        {
            $optionManager = new OptionManager([]);
            $optionManager->setGroupTable($sm->get(Groups::class));
            $optionManager->setOptionTable($sm->get(Options::class));
            $optionManager->buildconfig();
        }

        $optionManager = new OptionManager(include OptionManager::$fileconfig, true);
        $optionManager->setGroupTable($sm->get(Groups::class));
        $optionManager->setOptionTable($sm->get(Options::class));

        return $optionManager;
    }

}