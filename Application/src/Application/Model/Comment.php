<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Comment
{
	public $comment_id;
	public $comment;
	public $article_id;
	public $author_id;
	public $parent_id;
	public $ip_address;
	public $date_created;
	public $categories;
	
	public $article_alias;
	public $article_title;
	
	public $display_name;
	public $first_name;
	public $last_name;
	public $username;
	public $email;
	
	protected $inputFilter;
	    
    public function exchangeArray($data)
    {
		$this->comment_id	= (isset($data['comment_id'])) ? $data['comment_id'] : '';
		$this->comment		= (isset($data['comment'])) ? $data['comment'] : '';
		$this->article_id	= (isset($data['article_id'])) ? $data['article_id'] : '';
		$this->author_id	= (isset($data['author_id'])) ? $data['author_id'] : 1;
		$this->parent_id	= (isset($data['parent_id'])) ? $data['parent_id'] : '';
		$this->ip_address	= REMOTE_ADDR;
		$this->date_created	= (isset($data['date_created'])) ? $data['date_created'] : date('Y-m-d H:i:s');
		$this->status		= (isset($data['status'])) ? $data['status'] :'0';
		
		$this->article_alias		= (isset($data['article_alias'])) ? $data['article_alias'] :'';
		$this->article_title		= (isset($data['article_title'])) ? $data['article_title'] :'';
		
		$this->display_name		= (isset($data['display_name'])) ? $data['display_name'] :'';
		$this->first_name		= (isset($data['first_name'])) ? $data['first_name'] :'';
		$this->last_name		= (isset($data['last_name'])) ? $data['last_name'] :'';
		$this->username			= (isset($data['username'])) ? $data['username'] :'';
		$this->email			= (isset($data['email'])) ? $data['email'] :'';
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
                'name'     => 'comment_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

			$inputFilter->add($factory->createInput(array(
                'name'     => 'comment',
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
			    'name'     => 'article_id',
			    'required' => true,
			)));
			
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'author_id',
			    'required' => false,
			)));
			
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'parent_id',
			    'required' => false,
			)));
			
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'ip_address',
			    'required' => false,
			)));
			
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'status',
			    'required' => false,
			)));
			
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'date_created',
			    'required' => false,
			)));
			
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}