<? namespace XT\Option\Model;

use XT\Core\Filter\LatinLowCase;

class Option
{
    protected $id;
    protected $name;
    protected $description;
    protected $value;
    protected $type;
    protected $group_id;
    protected $hint;


    public function __construct()
    {
    }

    public function exchangeArray($data)
    {

        $this->id = (!empty($data['id'])) ? $data['id'] : 0;
        $this->value = (!empty($data['value'])) ? $data['value'] : '';
        $this->group_id = (!empty($data['group_id'])) ? $data['group_id'] : 0;
        $this->type = (!empty($data['type'])) ? $data['type'] : 'string';
        $this->name = (!empty($data['name'])) ? $data['name'] : '';
        $this->description = (!empty($data['description'])) ? $data['description'] : '';
        $this->hint = (!empty($data['hint'])) ? $data['hint'] : '';


    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * @param mixed $group_id
     */
    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;
    }

    /**
     * @return mixed
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * @param mixed $hint
     */
    public function setHint($hint)
    {
        $this->hint = $hint;
    }


}