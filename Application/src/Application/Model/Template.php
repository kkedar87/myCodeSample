<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Template
{
    public $template_id;
	public $alias;
	public $page_title;
	public $meta_title;
	public $meta_description;
	public $meta_keywords;
	public $description_heading;
	public $description;
	public $welcome_heading;
	public $welcome_message;
	public $nbaa_image_url;
	public $nbaa_image_alt;
	public $nbaa_image_title;
	public $main_content;
	public $additional_left_top;
	public $additional_left_bottom;
	public $additional_right_top;
	public $additional_right_bottom;
	public $status;
    protected $inputFilter;
	    
    public function exchangeArray($data)
    {
        $this->template_id = (isset($data['template_id'])) ? $data['template_id'] : '';
		$this->alias = (isset($data['alias'])) ? $data['alias'] : '';
		$this->page_title = (isset($data['page_title'])) ? $data['page_title'] : '';
		$this->meta_title = (isset($data['meta_title'])) ? $data['meta_title'] : '';
		$this->meta_description = (isset($data['meta_description'])) ? $data['meta_description'] : '';
		$this->meta_keywords = (isset($data['meta_keywords'])) ? $data['meta_keywords'] : '';
		$this->description_heading = (isset($data['description_heading'])) ? $data['description_heading'] : '';
		$this->description = (isset($data['description'])) ? $data['description'] : '';
		$this->welcome_heading = (isset($data['welcome_heading'])) ? $data['welcome_heading'] : '';
		$this->welcome_message = (isset($data['welcome_message'])) ? $data['welcome_message'] : '';
		$this->nbaa_image_url = (isset($data['nbaa_image_url'])) ? $data['nbaa_image_url'] : '';
		$this->nbaa_image_alt = (isset($data['nbaa_image_alt'])) ? $data['nbaa_image_alt'] : '';
		$this->nbaa_image_title = (isset($data['nbaa_image_title'])) ? $data['nbaa_image_title'] : '';
		$this->main_content = (isset($data['main_content'])) ? $data['main_content'] : '';
		$this->additional_left_top = (isset($data['additional_left_top'])) ? $data['additional_left_top'] : '';
		$this->additional_left_bottom = (isset($data['additional_left_bottom'])) ? $data['additional_left_bottom'] : '';
		$this->additional_right_top = (isset($data['additional_right_top'])) ? $data['additional_right_top'] : '';
		$this->additional_right_bottom = (isset($data['additional_right_bottom'])) ? $data['additional_right_bottom'] : '';
		$this->status = (isset($data['status'])) ? $data['status'] : '';
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
			    'name'     => 'template_id',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'alias',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'page_title',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'meta_title',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'meta_description',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'meta_keywords',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'description_heading',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'description',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'welcome_heading',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'welcome_message',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'nbaa_image_url',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'nbaa_image_alt',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'nbaa_image_title',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'main_content',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'additional_left_top',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'additional_left_bottom',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'additional_right_top',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'additional_right_bottom',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'status',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'date',
			    'required' => false,
			)));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}