<?php
require_once("model.php");

class TableCapiEquipe
{
	public $id;
	public $nome;
	public $email;

	public function __construct()
	{

	}
}

class CapiEquipe extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'capi_equipe';
		$this->table_model= "TableCapiEquipe";
		$this->statements= array("GET_BY_NOME"=>"SELECT * FROM {$this->table} WHERE nome LIKE ? LIMIT ?,?",
								"GET_BY_ID"=>"SELECT * FROM {$this->table} WHERE id_catena= ? LIMIT ?,?",
								"GET_BY_ID_SUPERMERCATO"=>"SELECT * FROM {$this->table} WHERE id_supermercato = ?",
								"GET_BY_SUPERMERCATI"=>"SELECT * FROM {$this->table} WHERE id_supermercato IN ?");
	}
}
?>
