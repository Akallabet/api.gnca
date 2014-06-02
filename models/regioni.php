<?php
require_once("model.php");

class RegioniTable
{
	public $id;
	public $nome;

	public function __construct()
	{

	}
}

class Regioni extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'regioni';
		$this->table_model= "RegioniTable";
		$this->statements= array("GET_BY_NOME"=>"SELECT * FROM {$this->table} WHERE nome LIKE ? LIMIT ?,?");
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
