<?php
require_once("model.php");

class TableCarico
{
	public $id;
	public $ordine;
	public $id_supermercato;

	public function __construct()
	{

	}
}

class Carichi extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'carichi';
		$this->table_model= "TableCarico";
		$this->statements= array(
			"GET_BY_ID_SUPERMERCATO"=>"SELECT * FROM {$this->table} A WHERE A.id_supermercato= ? LIMIT ?,?",
			"GET_BY_ORDINE"=>"SELECT * FROM {$this->table} A WHERE A.ordine= ? LIMIT ?,?");
	}

	function getByIdSupermercato($id_supermecato, $limit_from='',$limit_to='')
	{
		$id_supermecato= $this->sanitize($id_supermecato);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_SUPERMERCATO']);

		$statement->bind_param("sii",$id_supermecato,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}

	function getByOrdine($ordine, $limit_from='',$limit_to='')
	{
		$ordine= $this->sanitize($ordine);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ORDINE']);

		$statement->bind_param("sii",$ordine,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}
}
?>
