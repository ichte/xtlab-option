<?php


namespace XT\Option\Service\Factory;


use XT\Option\Model\Option;
use XT\Option\Service\Options;
use Zend\Db\Adapter\Adapter;

class OptionsFactory
{
    public function __invoke($serviceLocator)
    {

        $dbAdapter = $serviceLocator->get(Adapter::class);
        $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Option());
        return new Options('option_items', $dbAdapter, null, $resultSetPrototype);

    }
}