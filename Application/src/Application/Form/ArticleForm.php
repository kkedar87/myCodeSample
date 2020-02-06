<?php
/*******************************************************
 * Purpose: Create's Articles form
 * Created By: Kedar
 * Created On: May 30, 2014
 * Modified By: Kedar
 * Modified On: May 30, 2014
 */

namespace Application\Form;

use Zend\Form\Form;

class ArticleForm extends Form
{
    protected $dynamic_options;
   	
   	public function __construct($dynamic_options = array())
    {
        $this->dynamic_options = $dynamic_options;

        parent::__construct('article');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'add_form');
        $this->setAttribute('enctype', 'multipart/form-data');
        
       	$this->add(array(
            'name' 	=> 'article_id',
            'attributes' => array(
                'type'  => 'hidden',
            	'value'	=> 0,
           		'id' 	=> 'article_id',
            ),
        ));
        /* $this->add(array(
            'name' => 'alias',
            'attributes' => array(
                'type'  => 'text',
        		'id'	=> 'alias',
        		'onkeypress' => 'return isAlias(this, event)',
            ),
            'options' => array(
                'label' => 'URL Alias',
            ),
        )); */
        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type'  => 'text',
        		'id'  	=> 'title',
            ),
            'options' => array(
                'label' => 'Title',
            ),
        ));
       /*  $this->add(array(
            'name' => 'bullet1',
            'attributes' => array(
                'type'  => 'text',
        		'id'  	=> 'title',
            ),
            'options' => array(
                'label' => 'Title',
            ),
        ));
        $this->add(array(
            'name' => 'bullet2',
            'attributes' => array(
                'type'  => 'text',
        		'id'  	=> 'title',
            ),
            'options' => array(
                'label' => 'Title',
            ),
        ));
        $this->add(array(
            'name' => 'bullet3',
            'attributes' => array(
                'type'  => 'text',
        		'id'  	=> 'title',
            ),
            'options' => array(
                'label' => 'Title',
            ),
        ));
        $this->add(array(
            'name' => 'bullet4',
            'attributes' => array(
                'type'  => 'text',
        		'id'  	=> 'title',
            ),
            'options' => array(
                'label' => 'Title',
            ),
        ));
        $this->add(array(
            'name' => 'bullet5',
            'attributes' => array(
                'type'  => 'text',
        		'id'  	=> 'title',
            ),
            'options' => array(
                'label' => 'Title',
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type'  => 'textarea',
        		'id'	=> 'description',
            ),
            'options' => array(
                'label' => 'Description',
            ),
        ));
        $this->add(array(
            'name' => 'tags',
            'attributes' => array(
                'type'  => 'textarea',
        		'id'	=> 'tags',
            ),
            'options' => array(
                'label' => 'Associated Tags',
            ),
        ));
        $this->add(array(
            'name' => 'source',
            'attributes' => array(
                'type'  => 'text',
        		'id'  	=> 'source',
            ),
            'options' => array(
                'label' => 'Source',
            ),
        ));
        */
        $this->add(array(
            'type'  => '\Zend\Form\Element\Select',
            'name' => 'type',
            'attributes' => array(
                'id'	=> 'type',
            ),
            'options' => array(
                'label' => 'Type (Article or Blog Post)',
            	'options' => array(
        						array(
        							'value' => 'article',
									'label' => 'Article',
        							'selected' => true,
        						),
			        			array(
			        				'value' => 'blog',
			        				'label' => 'Blog',
			        				'selected' => false,
			        			),
        					),
            			),
        ));
        /*
        $this->add(array(
            'name' => 'image',
            'attributes' => array(
                'type'  => 'file',
        		'id'	=> 'image',
            ),
            'options' => array(
                'label' => 'Image',
            ),
        ));
        $this->add(array(
            'type'  => '\Zend\Form\Element\Select',
            'name' => 'categories',
            'attributes' => array(
                'id'	=> 'categories',
            	'multiple' => 'multiple',
            	'style' => 'height: 300px',	
            ),
            'options' => array(
                'label' => 'Select Categories',
            	'options' => $this->getCategoriesForSelect(),
            ),
        ));*/
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        )); 
    }
    public function getCategoriesForSelect()
    {
    	if(isset($this->dynamic_options['categories']))
    	{
    		return array_merge(array('0' => 'Select Categories'), $this->dynamic_options['categories']);
    	}
    }    
}