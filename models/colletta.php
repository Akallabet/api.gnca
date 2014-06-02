<?php
require_once("model.php");

class TableColletta
{
	public $id;
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
}
?>
