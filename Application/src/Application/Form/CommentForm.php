<?php
/*******************************************************
 * Purpose: Create's Comments form
 * Created By: Kedar
 * Created On: Jun 18, 2014
 * Modified By: Kedar
 * Modified On: Jun 18, 2014
 */

namespace Application\Form;

use Zend\Form\Form;

class CommentForm extends Form
{
    protected $dynamic_options;
   	
   	public function __construct($dynamic_options = array())
    {
        $this->dynamic_options = $dynamic_options;

        parent::__construct('comment');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'add_form');
        $this->setAttribute('enctype', 'multipart/form-data');
        
       	$this->add(array(
            'name' => 'comment_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'comment',
            'attributes' => array(
                'type'  => 'textarea',
        		'id'	=> 'comment',
            	'rows'	=> '4',
            ),
            'options' => array(
                'label' => 'Comment',
            ),
        ));
        $this->add(array(
            'type'  => '\Zend\Form\Element\Select',
            'name' => 'article_id',
            'attributes' => array(
                'id'	=> 'article_id',
            ),
            'options' => array(
                'label' => 'Select Article',
            	'options' =>  $this->getArticlesForSelect(),
            ),
        ));
        $this->add(array(
            'type'  => '\Zend\Form\Element\Select',
            'name' => 'author_id',
            'attributes' => array(
                'id'	=> 'author_id',
            ),
            'options' => array(
                'label' => 'Author of the Comment',
            	'options' =>  $this->getAuthorsForSelect(),
            ),
        ));
        $this->add(array(
            'type'  => '\Zend\Form\Element\Select',
            'name' => 'parent_id',
            'attributes' => array(
                'id'	=> 'parent_id',
            ),
            'options' => array(
                'label' => 'Select Parent Comment',
            	'options' =>  $this->getCommentsForSelect(),
            ),
        ));
        $this->add(array(
            'name' => 'comment_ip',
            'attributes' => array(
                'type'  => 'text',
        		'id'  	=> 'comment_ip',
            ),
            'options' => array(
                'label' => 'Cleint IP Address',
            ),
        ));
        $this->add(array(
        		'name' => 'date_created',
        		'attributes' => array(
        				'type'  => 'text',
        				'id'  	=> 'date_created',
        		),
        		'options' => array(
        				'label' => 'Created Date of the Comment',
        		),
        ));
       	$this->add(array(
            'type'  => '\Zend\Form\Element\Select',
            'name' => 'status',
            'attributes' => array(
                'id'	=> 'status',
            ),
            'options' => array(
                'label' => 'Status',
            	'options' => array('0' => 'Deactive', '1' => 'Active'),
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
    public function getArticlesForSelect()
    {
    	if(isset($this->dynamic_options['articles']))
    	{
    		return array('' => 'Select Article') + $this->dynamic_options['articles'];
    	}
    }    
    public function getCommentsForSelect()
    {
    	if(isset($this->dynamic_options['comments']))
    	{
    		return array('0' => 'Select Parent Comment') + $this->dynamic_options['comments'];
    	}
    }    
    public function getAuthorsForSelect()
    {
    	if(isset($this->dynamic_options['authors']))
    	{
    		return array('0' => 'Select Author') + $this->dynamic_options['authors'];
    	}
    }    
}