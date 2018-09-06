<?

namespace XT\Option\Form;

use XT\Core\Common\Common;
use XT\Core\Filter\LatinLowCase;
use Zend\Db\Adapter\Adapter;
use Zend\Filter\StringToLower;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\Whitelist;
use Zend\Form\Element\Select;
use Zend\Form\Form;
use Zend\I18n\Filter\Alnum;
use Zend\I18n\Validator\Alpha;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class OptionForm extends \XT\Core\Form\Form
{
    public function __construct($name = null)
    {

        parent::__construct('optionform');

        $this->add([
            'name' => 'name',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control'
            ],

        ]);
        $this->add([
            'name' => 'hint',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control'
            ],
        ]);

        $this->add([
            'name' => 'type',
            'type' => Select::class,
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [

                'empty_option' => 'Please select type of option',
                'value_options' => [
                    'string' => 'Text',
                    'integer' => 'Interger',
                    'numeric' => 'Numeric',
                    'array' => 'Array',
                    'boolean' => 'True/False',
                    'positive_integer' => 'Positive integer',
                    'unsigned_integer' => 'Unsigned_integer',
                    'unsigned_numeric' => 'Unsigned_numeric'
                ],
            ]
        ]);


        $this->add([
            'name' => 'description',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control'
            ],
        ]);

        $this->add([
            'name' => 'group_id',
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
            'name' => 'type',
            'filters' => [
                [
                    'name' => Whitelist::class,
                    'options' => [
                        'list' => ['string', 'integer', 'numeric', 'array', 'boolean', 'positive_integer', 'unsigned_integer', 'unsigned_numeric']
                    ]
                ],
            ],
            'validators' => [
                ['name' => NotEmpty::class],

            ],
        ]);

        $inputFilter->add([
            'name' => 'name',
            'filters' => [
                ['name' => LatinLowCase::class],
                ['name' => Alnum::class],
                ['name' => StringTrim::class,
                    'options' => [
                        'charlist' => " ,#,$"
                    ]
                ],
                ['name' => StringToLower::class,
                    'options' => [
                        'encoding' => "UTF-8"
                    ]
                ],
            ],
            'validators' => [
                ['name' => Alpha::class],
                ['name' => NotEmpty::class],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 30,
                    ],

                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'hint',
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                ['name' => NotEmpty::class],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 250,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'description',
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [

                ['name' => NotEmpty::class],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 0,
                        'max' => 250,
                    ],
                ],
            ],
        ]);


        $this->setInputFilter($inputFilter);
    }

    /***
     * @param array $exclude ['field' => col, 'value' => val]
     */
    public function addNoRecordExistsName($exclude = null)
    {

        $validatorchain = $this->getInputFilter()->get('name')->getValidatorChain();


        $validator_checkdatabase = new NoRecordExists(
            [
                'table' => 'option_items',
                'field' => 'name',
                'adapter' => Common::$sm->get(Adapter::class)
            ]
        );

        if ($exclude != null)
            $validator_checkdatabase->setExclude($exclude);

        $validatorchain->addValidator($validator_checkdatabase);
    }
}