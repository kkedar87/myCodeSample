<?php
namespace Application\Form;

use Zend\Form\Form;

class MemberForm extends Form
{
    protected $dynamic_options;
   	
   	public function __construct($dynamic_options = array())
    {
        $this->dynamic_options = $dynamic_options;

        parent::__construct('member');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'add_form');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        $this->add(array(
            'name' => 'first_name',
            'attributes' => array(
                'type'  => 'text',
        		'id'  	=> 'first_name',
            ),
            'options' => array(
                'label' => 'First Name',
            ),
        ));
        $this->add(array(
            'name' => 'last_name',
            'attributes' => array(
                'type'  => 'text',
        		'id'  	=> 'last_name',
            ),
            'options' => array(
                'label' => 'Last Name',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'text',
        		'id'	=> 'email',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
        		'id'	=> 'password',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));
        $this->add(array(
            'name' => 'confirm_password',
            'attributes' => array(
                'type'  => 'password',
        		'id'	=> 'confirm_password',
            ),
            'options' => array(
                'label' => 'Confirm Password',
            ),
        ));
        $this->add(array(
            'type'  => '\Zend\Form\Element\Radio',
            'name' => 'role',
            'attributes' => array(
                'id'	=> 'role',
            ),
            'options' => array(
                'label' => 'User Type',
            	'options' => $this->getUserRoles(),
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }
    public function getUserRoles()
    {
    	if(isset($this->dynamic_options['role_types']))
    	{
    		return array_merge($this->dynamic_options['role_types']);
    	}
    }    
}