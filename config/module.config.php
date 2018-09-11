<?php
return [
    'admin_plugins' => [
        'options' => \XT\Option\Admin\OptionsAdmin::class,
    ],

    'service_manager' => [
        'factories' => [
            \XT\Option\Service\Groups::class => \XT\Option\Service\Factory\GroupsFactory::class,
            \XT\Option\Service\Options::class => \XT\Option\Service\Factory\OptionsFactory::class,
            \XT\Option\Service\OptionManager::class => \XT\Option\Service\Factory\OptionManagerFactory::class,
            \XT\Option\Service\Factory\OptionAccess::class => \XT\Option\Service\Factory\OptionAccess::class
        ],

    ],



];