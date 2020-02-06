<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class Company
{
	public $company_id;
	public $name;
	public $symbol;
	public $currency;
	public $exchange;
	
    public function exchangeArray($data)
    {
		$this->company_id	= (isset($data['company_id'])) ? $data['company_id'] :'';
		$this->name 		= (isset($data['name'])) ? $data['name'] :'';
		$this->symbol 		= (isset($data['symbol'])) ? $data['symbol'] :'';
		$this->currency		= (isset($data['currency'])) ? $data['currency'] :'';
		$this->exchange		= (isset($data['exchange'])) ? $data['exchange'] :'';
    }
}