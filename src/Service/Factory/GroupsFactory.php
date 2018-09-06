<?php


namespace XT\Option\Service\Factory;


use XT\Option\Model\Group;
use XT\Option\Service\Groups;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;

class GroupsFactory
{
    public function __invoke($serviceLocator)
    {
        $dbAdapter = $serviceLocator->get(Adapter::class);
        $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Group());

        return new Groups('option_groups', $dbAdapter, null, $resultSetPrototype);

    }
}