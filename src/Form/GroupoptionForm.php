<?

namespace XT\Option\Form;

use XT\Core\Filter\LatinLowCase;
use XT\Core\Form\Element\TextareaAutoGrow;
use XT\Core\Form\Element\TextAutoComplete;
use XT\Core\Form\Form;
use Zend\I18n\Validator\Alpha;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\Db\RecordExists;

class GroupoptionForm extends Form
{
    public function __construct($name, $adapter)
    {
        // we want to ignore the name passed

        parent::__construct('groupoption');

        $this->add([
            'name' => 'name',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control'
            ],

        ]);
        $this->add([
            'name' => 'description',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control'
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Save',
                'id' => 'submitbutton',
                'class' => 'btn btn-secondary'
            ],
        ]);


        $inputFilter = new \Zend\InputFilter\InputFilter();

        $inputFilter->add([
            'name' => 'name',
            'filters' => [
                ['name' => LatinLowCase::class],
                ['name' => 'Alnum'],
                ['name' => 'StringTrim',
                    'options' => [
                        'charlist' => " ,#,$"
                    ]
                ],
                ['name' => 'StringToLower',
                    'options' => [
                        'encoding' => "UTF-8"
                    ]
                ],
            ],
            'validators' => [
                ['name' => 'NotEmpty'],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 50,
                    ],

                ],
                ['name' => Alpha::class, 'options' => ['allowWhiteSpace' => false]],
                [
                    'name' => ($name != 'edit') ? NoRecordExists::class : RecordExists::class,
                    'options' => [
                        'adapter' => $adapter,
                        'table' => 'option_groups',
                        'field' => 'name'
                    ]
                ]
            ],
        ]);

        $inputFilter->add([
            'name' => 'description',
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty'],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 255,
                    ],
                ],
            ],
        ]);


        $this->setInputFilter($inputFilter);


    }
}