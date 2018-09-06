<?
namespace XT\Option\Service;


 use XT\Option\Model\Option;
 use Zend\Db\TableGateway\TableGateway;

 class Options extends TableGateway
 {
   

     public function saveOptionValue($name,$val)
     {
            $boolfillter    = new \Zend\Filter\Boolean();
            $filter_num     = new \Zend\Filter\ToInt();
            $trim           = new \Zend\Filter\StringTrim();

            $r=$this->findByname($name);
            if ($r == null)
                return;
            $val = $trim->filter($val);
            if ($r->getType()=='bolean')  $val = $boolfillter->filter($val);
            else
            if ($r->getValue()=='integer') $val = $filter_num->filter($val);

            $this->update(['value'=>$val], ['name'=>$name]);



     }

     /**
      * @param $id_option
      * @return array|\ArrayObject|Option
      */
     public function find($id) {
         return $this->select(['id' => $id])->current();
     }

     /**
      * @param $id_option
      * @return array|\ArrayObject|Option
      */
     public function findByname($name) {
         return $this->select(['name' => $name])->current();
     }


 }