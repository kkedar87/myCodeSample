<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Msg
{
	public $msg_id;
	public $parent_id;
	public $send_to_type;
	public $to;
	public $to_multi;
	public $to_role;
	public $from;
	public $sub;
	public $msg;
	public $status;
	public $read;
	public $date;
	public $from_username;
	public $from_email;
	public $to_username;
	public $to_email;
	
	protected $inputFilter;
	    
    public function exchangeArray($data)
    {
		$this->msg_id		= (isset($data['msg_id'])) ? $data['msg_id'] :'';
		$this->parent_id	= (isset($data['parent_id'])) ? $data['parent_id'] :0;
		$this->send_to_type = (isset($data['send_to_type'])) ? $data['send_to_type'] :'';
		$this->to 			= (isset($data['to'])) ? $data['to'] :'';
		$this->to_multi 	= (isset($data['to_multi'])) ? $data['to_multi'] :'';
		$this->to_role 		= (isset($data['to_role'])) ? $data['to_role'] :'';
		$this->from 		= (isset($data['from'])) ? $data['from'] : 0;
		$this->sub 			= (isset($data['sub'])) ? $data['sub'] :'';
		$this->msg 			= (isset($data['msg'])) ? $data['msg'] :'';
		$this->read		 	= (isset($data['read'])) ? $data['read'] : '0';
		$this->status 		= (isset($data['status'])) ? $data['status'] :'1';
		$this->date 		= (isset($data['date'])) ? $data['date'] : date('Y-m-d H:i:s');
		$this->from_username= (isset($data['from_username'])) ? $data['from_username'] : '';
		$this->from_email 	= (isset($data['from_email'])) ? $data['from_email'] : '';
		$this->to_username 	= (isset($data['to_username'])) ? $data['to_username'] : '';
		$this->to_email 	= (isset($data['to_email'])) ? $data['to_email'] : '';
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

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}