<?php
require_once("model.php");

class TableCatene
{
	public $id;
	public $nome;

	public function __construct()
	{

	}
}

class Catene extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'catene';
		$this->table_model= "TableCatene";
		$this->statements= array("GET_BY_NOME"=>"SELECT * FROM {$this->table} WHERE nome LIKE ? LIMIT ?,?",
								"GET_BY_ID"=>"SELECT * FROM {$this->table} WHERE id= ? LIMIT ?,?");
	}

	function getByNome($nome, $limit_from='',$limit_to='')
	{
		$nome= $this->sanitize($nome)."%";
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_NOME']);

		$statement->bind_param("sii",$nome,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}
}
?>
