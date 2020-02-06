<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Page
{
    public $page_id;
    public $alias;
    public $title;
    public $content;
	public $meta_title;
	public $meta_description;
	public $meta_keywords;
	public $image;
	public $sortorder;
	public $status;
	protected $inputFilter;
	    
    public function exchangeArray($data)
    {
        $this->page_id			= (isset($data['page_id'])) ? $data['page_id'] : '';
        $this->alias 			= (isset($data['alias'])) ? $data['alias'] : '';
        $this->title 			= (isset($data['title'])) ? $data['title'] : '';
        $this->content	 		= (isset($data['content'])) ? $data['content'] : '';
        $this->meta_title 		= (isset($data['meta_title'])) ? $data['meta_title'] : '';
        $this->meta_description = (isset($data['meta_description'])) ? $data['meta_description'] : '';
        $this->meta_keywords 	= (isset($data['meta_keywords'])) ? $data['meta_keywords'] : '';
        $this->image			= (isset($data['image'])) ? is_array($data['image'])?$data['image']['name']:$data['image']: '';
        $this->sortorder		= (isset($data['sort_order'])) ? $data['sort_order'] : 0;	       
        $this->status			= (isset($data['status'])) ? $data['status'] : 0;	       
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
                'name'     => 'page_id',
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
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'title',
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
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'content',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                        ),
                    ),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
                'name'     => 'meta_description',
                'required' => false,
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
                'name'     => 'meta_keywords',
                'required' => false,
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
                'name'     => 'image',
                'required' => false,
                
            )));
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}