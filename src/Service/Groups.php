<?php

namespace XT\Option\Service;


use XT\Core\Common\Common;
use XT\Db\Adapter;
use XT\Option\Model\Group;
use Zend\Db\TableGateway\TableGateway;

class Groups extends TableGateway
{
    /**
     * @param $id
     * @return array|\ArrayObject|Group
     */
    public function find($id) {
        return $this->select(['id' => $id])->current();
    }

    /**
     * @param $id
     * @return boolean
     */
    public function isExist($id) {
        /**
         * @var $dbAdapter Adapter
         */
        $dbAdapter = $this->getAdapter();
        return $dbAdapter->existrow(['id' => $id], $this->getTable());

    }

    /**
     * @param $id
     * @return Group
     */
    public function findByName($name) {
        return $this->select(['name' => $name])->current();
    }

    /**
     * @param $id
     * @return boolean
     */
    public function isExistName($name) {
        /**
         * @var $dbAdapter Adapter
         */
        $dbAdapter = $this->getAdapter();
        return $dbAdapter->existrow(['name' => $name], $this->getTable());

    }

    public function delete($where)
    {
        /***
         * @var $options \XT\Option\Service\Options
         */
        $options = Common::$sm->get(Options::class);

        /***
         * @var $group Group
         */
        foreach ($this->select($where) as $group) {
            $c = $options->select(['group_id' => $group->getId()])->count();
            if ($c != 0)
                return 0;
        }
        

        return parent::delete($where);
    }


}