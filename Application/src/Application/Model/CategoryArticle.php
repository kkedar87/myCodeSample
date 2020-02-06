<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class CategoryArticle
{
	public $id;
	public $category_id;
	public $article_id;
	public $cat_name;
	public $cat_alias;
	
	protected $inputFilter;
	    
    public function exchangeArray($data)
    {
		$this->id			= (isset($data['id'])) ? $data['id'] : '';
		$this->category_id	= (isset($data['category_id'])) ? $data['category_id'] : '';
		$this->article_id	= (isset($data['article_id'])) ? $data['article_id'] : '';
		$this->cat_name		= (isset($data['cat_name'])) ? $data['cat_name'] : '';
		$this->cat_alias	= (isset($data['cat_alias'])) ? $data['cat_alias'] : '';
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
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'article_id',
            		'required' => true,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'category_id',
            		'required' => true,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}