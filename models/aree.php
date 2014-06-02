<?php
require_once("model.php");

class TableArea
{
	public $id;
	public $nome;

	public function __construct()
	{

	}
}

class Aree extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'aree';
		$this->table_model= "TableArea";
	}
}
?>
