<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Member
{
	public $user_id;
	public $first_name;
	public $last_name;
	public $username;
	public $email;
	public $display_name;
	public $password;
	public $role;
	public $state;
	public $dob;
	public $company;
	public $company_url;
	public $company_info;
	public $about;
	public $status;
	public $date_created;
	public $last_login;
	public $date_modified;
	public $image;
	public $del_image;
	public $verificationcode;
	public $article_count;
	
	protected $inputFilter;
	    
    public function exchangeArray($data)
    {
		$this->user_id		= (isset($data['user_id'])) ? $data['user_id'] :'';
		$this->first_name 	= (isset($data['first_name'])) ? $data['first_name'] :'';
		$this->last_name 	= (isset($data['last_name'])) ? $data['last_name'] :'';
		$this->username 	= (isset($data['username'])) ? $data['username'] :'';
		$this->email 		= (isset($data['email'])) ? $data['email'] :'';
		$this->display_name = (isset($data['display_name'])) ? $data['display_name'] :'';
		$this->password 	= (isset($data['password'])) ? $data['password'] :'';
		$this->role 		= (isset($data['role'])) ? $data['role'] :'';
		$this->state 		= (isset($data['state'])) ? $data['state'] :'';
		$this->dob 			= (isset($data['dob'])) ? $data['dob'] :'0000-00-00 00:00:00';
		$this->company 		= (isset($data['company'])) ? $data['company'] :'';
		$this->company_url 	= (isset($data['company_url'])) ? $data['company_url'] :'';
		$this->company_info = (isset($data['company_info'])) ? $data['company_info'] :'';
		$this->about 		= (isset($data['about'])) ? $data['about'] :'';
		$this->status 		= (isset($data['status'])) ? $data['status'] :'1';
		$this->date_created = (isset($data['date_created'])) ? $data['date_created'] : date('Y-m-d H:i:s');
		$this->last_login 	= (isset($data['last_login'])) ? $data['last_login'] :'0000-00-00 00:00:00';
		$this->date_modified= (isset($data['date_modified'])) ? $data['date_modified'] : date('Y-m-d H:i:s');
		$this->image 		= (isset($data['image'])) ? is_array($data['image'])?$data['image']['name']:$data['image']: '';
		$this->del_image 	= (isset($data['del_image'])) ? $data['del_image'] : '0';
		$this->article_count 	= (isset($data['article_count'])) ? $data['article_count'] : '0';
		$this->verificationcode 	= (isset($data['verificationcode'])) ? $data['verificationcode'] : '';
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
                'name'     => 'email',
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
                'name'     => 'role',
                'required' => true,
            )));
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'password',
                'required' => true,
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'confirm_password',
            		'required' => true,
            )));
            	
			$inputFilter->add($factory->createInput(array(
							'name'     => 'first_name',
							'required' => false,
						)));
			$inputFilter->add($factory->createInput(array(
							'name'     => 'last_name',
							'required' => false,
						)));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}