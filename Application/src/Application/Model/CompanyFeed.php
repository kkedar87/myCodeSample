<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;     // <-- Add this import
use Zend\InputFilter\InputFilter;                 // <-- Add this import
use Zend\InputFilter\InputFilterAwareInterface;   // <-- Add this import
use Zend\InputFilter\InputFilterInterface;        // <-- Add this import

class CompanyFeed
{
	public $id;
	public $symbol;
	public $exchange;
	public $opening_price;
	public $high_price;
	public $low_price;
	public $last_price;
	public $volume;
	public $split_ratio;
	public $cash_dividend;
	public $update_date;
	public $update_time;
	public $trading_curreny;
	public $dividend_currency;
	public $date;
	
	protected $inputFilter;
	    
    public function exchangeArray($data)
    {
		$this->id				= (isset($data['id'])) ? $data['id'] :'';
		$this->symbol 			= (isset($data['symbol'])) ? $data['symbol'] :'';
		$this->exchange 		= (isset($data['exchange'])) ? $data['exchange'] :'';
		$this->opening_price	= (isset($data['opening_price'])) ? $data['opening_price'] :'';
		$this->high_price 		= (isset($data['high_price'])) ? $data['high_price'] :'';
		$this->low_price 		= (isset($data['low_price'])) ? $data['low_price'] :'';
		$this->last_price 		= (isset($data['last_price'])) ? $data['last_price'] :'';
		$this->volume 			= (isset($data['volume'])) ? $data['volume'] :'';
		$this->split_ratio 		= (isset($data['split_ratio'])) ? $data['split_ratio'] :'';
		$this->cash_dividend 	= (isset($data['cash_dividend'])) ? $data['cash_dividend'] :'';
		$this->update_date 		= (isset($data['update_date'])) ? $data['update_date'] :'';
		$this->update_time 		= (isset($data['update_time'])) ? $data['update_time'] :'';
		$this->trading_curreny 	= (isset($data['trading_curreny'])) ? $data['trading_curreny'] :'';
		$this->dividend_currency= (isset($data['dividend_currency'])) ? $data['dividend_currency'] :'';
		$this->date 			= (isset($data['date'])) ? $data['date']:date('Y-m-d H:i:s');
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
}