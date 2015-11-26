<?php
require_once("model.php");

class TableColletta
{
	public $id;
	public $nome;
	public $anno;
	public $data;
	public $attiva;

	public function __construct()
	{

	}
}

class Colletta extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'colletta';
		$this->table_model= "TableColletta";
	}

	function getActive()
	{
		$query= "SELECT * FROM {$this->table} WHERE attiva='1'";
		$res= $this->executeStandardQuery($query);
		return $res;
	}
}
?>
