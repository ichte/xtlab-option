<?php

namespace XT\Option\Service;


use XT\Option\Model\Group;
use XT\Option\Model\Option;
use Zend\Config\Config;

class OptionManager extends Config
{
    public static $fileconfig = 'config/xtlab.option.php';
    /**
     * @var Groups
     */
    protected $groupTable;
    /**
     * @var Options
     */
    protected $optionTable;

    /**
     * @param mixed $groupTable
     */
    public function setGroupTable($groupTable)
    {
        $this->groupTable = $groupTable;
    }

    /**
     * @param mixed $optionTable
     */
    public function setOptionTable($optionTable)
    {
        $this->optionTable = $optionTable;
    }



    public function buildconfig()
    {

        $grouptb  =   $this->groupTable;
        $optiontb =  $this->optionTable;
        $groups   = $grouptb->select();
        $arrayconfig = [];
        /**
         * @param $op Option
         * @return bool|int
         */
        $getval = function($op)
        {
            switch ($op->getType()){
                case 'integer':
                    return (int)$op->getValue();
                    break;
                case 'boolean':
                    return (bool)$op->getValue();
                    break;
                default: return $op->getValue(); break;
            }
        };

        /***
         * @var $gr Group
         */
        foreach ($groups as $gr)
        {
            $ar = [];


            $ops = $optiontb->select(['group_id'=>$gr->getId()]);
            foreach ($ops as $op)
            {
                /**
                 * @var $op Option
                 */

                $ar[$op->getName()] = $getval($op);
            }



            $arrayconfig[$gr->getName()] = $ar;
        }


        $arrayconfig = ['CF'=>$arrayconfig];



        $config = new \Zend\Config\Config($arrayconfig, true);

        $config->CF->common->pathtemplatedefault = "__DIR__.\"/../module/Application/template\"";
        $config->CF->common->rootpath = "__DIR__.\"/ROOTPATH_REPLACE/\"";


        //Create folder backup
        if (!file_exists(OptionManager::$fileconfig.'.back'))
            mkdir(OptionManager::$fileconfig.'.back');


        $backupfile = OptionManager::$fileconfig.'.back/'.time().'.bak';
        

        if (file_exists(OptionManager::$fileconfig))
            rename(OptionManager::$fileconfig, $backupfile);

        $writer = new \Zend\Config\Writer\PhpArray();

        $writer->toFile(OptionManager::$fileconfig,$config);

        $content = file_get_contents(OptionManager::$fileconfig);
        $content = str_replace('\'__DIR__."/../module/Application/template"\'','__DIR__."/../module/Application/template"' ,$content);
        $content = str_replace('\'__DIR__."/ROOTPATH_REPLACE/"\'','realpath(__DIR__.\'/../\')' ,$content);

        $byte = file_put_contents(OptionManager::$fileconfig,$content);
        if ($byte === false)  throw new \Exception("Can not save: config/xtlab.option.php");
 
        return $arrayconfig;

    }

    public function listoptions()
    {
        $grouptb =   $this->groupTable;
        $optiontb =  $this->optionTable;
        $groups = $grouptb->select();
        $arrayconfig = [];
        /**
         * @param $op Option
         * @return bool|int
         */

        /***
         * @var $gr Group
         */
        foreach ($groups as $gr)
        {

            $ar = [];
            $ops = $optiontb->select(['group_id'=>$gr->getId()]);
            foreach ($ops as $op)
            {
                $ar[] = $op;
            }

            $arrayconfig[] = [
                'group' => $gr,
                'item' => $ar
            ];
        }

        return $arrayconfig;

    }
}