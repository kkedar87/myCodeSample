<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Curated
{
    public $curated_id;
	public $title;
	public $description;
	public $link;
	public $date;
	public $status;
    protected $inputFilter;
	    
    public function exchangeArray($data)
    {
        $this->curated_id = (isset($data['curated_id'])) ? $data['curated_id'] : '';
		$this->title = (isset($data['title'])) ? $data['title'] : '';
		$this->description = (isset($data['description'])) ? $data['description'] : '';
		$this->link = (isset($data['link'])) ? $data['link'] : '';
		$this->status = (isset($data['status'])) ? $data['status'] : '';
		$this->date = (isset($data['date'])) ? $data['date'] : date('Y-m-d H:i:s');
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
			    'name'     => 'curated_id',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'title',
			    'required' => true,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'description',
			    'required' => false,
			)));
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'link',
			    'required' => true,
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