<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Article
{
	public $article_id;
	public $alias;
	public $type;
	public $author_id;
	public $title;
	public $bullet1;
	public $bullet2;
	public $bullet3;
	public $bullet4;
	public $bullet5;
	public $description;
	public $tickers;
	public $primary_ticker;
	public $secondary_ticker;
	public $category;
	public $position_dislosure;
	public $position_types;
	public $position_stocks;
	public $position_other_info;
	public $business_rel_disclosure;
	public $business_rel_not_own_specify;
	public $user_site_url;
	public $user_site_name;
	public $mini_bio;
	public $visits;
	public $status;
	public $step;
	public $date_created;
	
	public $categories;
	public $user_image;
	public $username;
	public $email;
	public $comments;
	public $display_name;
	public $first_name;
	public $last_name;
	public $editors_choice;
	public $curated_article;
	public $curated_source;
	public $last_edited_by;
	public $date_edited;
	public $cat_names;
	public $cat_aliases;
		
	public $del_image;
	
	protected $inputFilter;
	    
    public function exchangeArray($data)
    {
		$this->article_id=(isset($data['article_id']))?$data['article_id']:'';
		$this->alias=(isset($data['alias']))?$data['alias']:'';
		$this->type = (isset($data['type'])) ? $data['type'] : '';
		$this->author_id = (isset($data['author_id'])) ? $data['author_id'] : '';
		$this->title=(isset($data['title']))?$data['title']:'';
		$this->bullet1=(isset($data['bullet1']))?$data['bullet1']:'';
		$this->bullet2=(isset($data['bullet2']))?$data['bullet2']:'';
		$this->bullet3=(isset($data['bullet3']))?$data['bullet3']:'';
		$this->bullet4=(isset($data['bullet4']))?$data['bullet4']:'';
		$this->bullet5=(isset($data['bullet5']))?$data['bullet5']:'';
		$this->description=(isset($data['description']))?$data['description']:'';
		$this->tickers=(isset($data['tickers']))?$data['tickers']:'';
		$this->primary_ticker=(isset($data['primary_ticker']))?$data['primary_ticker']:'';
		$this->secondary_ticker=(isset($data['secondary_ticker'])?(is_array($data['secondary_ticker'])?implode(", ", $data['secondary_ticker']):$data['secondary_ticker']):'');
		$this->category=(isset($data['category']))?$data['category']:'';
		$this->position_dislosure=(isset($data['position_dislosure']))?$data['position_dislosure']:'';
		$this->position_types=(isset($data['position_types']))?$data['position_types']:'';
		$this->position_stocks=(isset($data['position_stocks']))?$data['position_stocks']:'';
		$this->position_other_info=(isset($data['position_other_info']))?$data['position_other_info']:'';
		$this->business_rel_disclosure=(isset($data['business_rel_disclosure']))?$data['business_rel_disclosure']:'';
		$this->business_rel_not_own_specify=(isset($data['business_rel_not_own_specify']))?$data['business_rel_not_own_specify']:'';
		$this->user_site_url=(isset($data['user_site_url']))?$data['user_site_url']:'';
		$this->user_site_name=(isset($data['user_site_name']))?$data['user_site_name']:'';
		$this->mini_bio=(isset($data['mini_bio']))?$data['mini_bio']:'';
		$this->visits=(isset($data['visits']))?$data['visits']:'';
		$this->status=(isset($data['status']))?$data['status']:'';
		$this->step=(isset($data['step']))?$data['step']:'';
		$this->date_created=(isset($data['date_created']))?$data['date_created']:date('Y-m-d H:i:s');
		
		$this->categories		= (isset($data['categories'])) ? $data['categories'] : array();
		$this->user_image		= (isset($data['user_image'])) ? $data['user_image'] : '';
		$this->username			= (isset($data['username'])) ? $data['username'] : '';
		$this->email			= (isset($data['email'])) ? $data['email'] : '';
		$this->comments			= (isset($data['comments'])) ? $data['comments'] : 0;
		$this->display_name		= (isset($data['display_name'])) ? $data['display_name'] : '';
		$this->first_name		= (isset($data['first_name'])) ? $data['first_name'] : '';
		$this->last_name		= (isset($data['last_name'])) ? $data['last_name'] : '';
		$this->editors_choice	=(isset($data['editors_choice']))?$data['editors_choice']:'0';
		$this->curated_article	= (isset($data['curated_article'])) ? $data['curated_article'] : '0';
		$this->curated_source	= (isset($data['curated_source'])) ? $data['curated_source'] : '';
		$this->last_edited_by	= (isset($data['last_edited_by'])) ? $data['last_edited_by'] : '';
		$this->date_edited		= (isset($data['date_edited']) && $data['date_edited'] != '0000-00-00 00:00:00') ? $data['date_edited'] : date('Y-m-d H:i:s');
		$this->cat_names		= (isset($data['cat_names'])) ? $data['cat_names'] : '';
		$this->cat_aliases		= (isset($data['cat_aliases'])) ? $data['cat_aliases'] : '';
		
		$this->del_image 		= (isset($data['del_image'])) ? $data['del_image'] : 0;
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
                'name'     => 'article_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
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
                            'max'      => 255,
                        ),
                    ),
                ),
            )));
            
           /*  $inputFilter->add($factory->createInput(array(
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
                            'max'      => 255,
                        ),
                    ),
                ),
            )));
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'description',
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
                'name'     => 'bullet1',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'bullet2',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'bullet3',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'bullet4',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'bullet5',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'tickers',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'primary_ticker',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'secondary_ticker',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'position_dislosure',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'position_types',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'position_stocks',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'position_other_info',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'business_rel_disclosure',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'business_rel_not_own_specify',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'user_site_url',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'user_site_name',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'mini_bio',
                'required' => false,
            )));
			$inputFilter->add($factory->createInput(array(
                'name'     => 'visits',
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
             */
            
			$inputFilter->add($factory->createInput(array(
			    'name'     => 'type',
			    'required' => false,
			)));

			/* $inputFilter->add($factory->createInput(array(
			    'name'     => 'author_id',
			    'required' => false,
			)));
			
            $inputFilter->add($factory->createInput(array(
                'name'     => 'del_image',
                'required' => false,
            ))); */

			$inputFilter->add($factory->createInput(array(
					'name'     => 'submit',
					'required' => false,
			)));
			
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}