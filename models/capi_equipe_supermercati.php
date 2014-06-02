<?php
require_once("model.php");

class TableCapiEquipeSupermercati
{
	public $id_capo_equipe;
	public $id_supermercato;

	public function __construct()
	{

	}
}

class CapiEquipeSupermercati extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'capi_equipe_supermercati';
		$this->table_model= "TableCapiEquipeSupermercati";
		$this->statements= array("GET_BY_NOME"=>"SELECT * FROM {$this->table} WHERE nome LIKE ? LIMIT ?,?",
								"GET_BY_ID"=>"SELECT * FROM {$this->table} WHERE id_catena= ? LIMIT ?,?",
								"GET_BY_ID_SUPERMERCATO"=>"SELECT * FROM {$this->table} WHERE id_supermercato = ?",
								"GET_BY_SUPERMERCATI"=>"SELECT * FROM {$this->table} WHERE id_supermercato IN ?");
	}
}
?>
