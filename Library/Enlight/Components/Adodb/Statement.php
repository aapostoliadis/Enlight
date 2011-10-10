<?php
class	Enlight_Components_Adodb_Statement extends Enlight_Class
{
	protected $_statement;
	
	public $fields = array();
	public $EOF = true;
	
	public function __construct($statement)
    {
        $this->_statement = $statement;
        $this->MoveNext();
    }
		
	public function RecordCount()
	{
		return $this->_statement->rowCount();
	}
	
	public function MoveNext()
	{
		if($this->_statement->columnCount()) {
			$this->fields = $this->_statement->fetch();
			$this->EOF = $this->fields===false;
		}
	}
	
	public function FetchRow()
	{
		if($this->fields) {
			$result = $this->fields;
			$this->fields = array();
			return $result;
		}
		return $this->_statement->fetch(Zend_Db::FETCH_ASSOC);
	}
	
	public function Close()
	{
		return $this->_statement->closeCursor();
	}
}