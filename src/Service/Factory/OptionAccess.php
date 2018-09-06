<?php

namespace XT\Option\Service\Factory;


use XT\Option\Service\OptionManager;
use Zend\Config\Config;

class OptionAccess
{
    public function __invoke($sm)
    {
        return file_exists(OptionManager::$fileconfig) ?
            new Config(include OptionManager::$fileconfig, true) :
            new Config([], true);
    }

}