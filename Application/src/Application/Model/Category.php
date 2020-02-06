<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Category
{
    public $category_id;
    public $alias;
    public $name;
    public $description;
	public $parent_id;
	public $sortoder;
	public $status;
	protected $inputFilter;
	    
    public function exchangeArray($data)
    {
        $this->category_id	= (isset($data['category_id'])) ? $data['category_id'] : '';
        $this->name 		= (isset($data['name'])) ? $data['name'] : '';
        $this->alias 		= (isset($data['alias'])) ? $data['alias'] : '';
        $this->description 	= (isset($data['description'])) ? $data['description'] : '';
        $this->image		= (isset($data['image'])) ? is_array($data['image'])?$data['image']['name']:$data['image']: '';
        $this->parent_id 	= (isset($data['parent_id'])) ? $data['parent_id'] : 0;
        $this->del_image 	= (isset($data['del_image'])) ? $data['del_image'] : 0;
        $this->sortorder 	= (isset($data['sortorder'])) ? $data['sortorder'] : 0;
        $this->status 		= (isset($data['status'])) ? $data['status'] : 1;
    }
	
    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'category_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'alias',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 255,
                        ),
                    ),
                ),
            )));
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 255,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'description',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'parent_id',
                'required' => false,
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'del_image',
                'required' => false,
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'image',
                'required' => false,
                
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}