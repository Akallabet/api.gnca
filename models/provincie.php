<?php
require_once("model.php");

class ProvinceTable
{
	public $id;
	public $nome;
	public $id_regione;

	public function __construct()
	{

	}
}

class Provincie extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'province';
		$this->table_model= "ProvinceTable";
		$this->statements= array("GET_BY_NOME"=>"SELECT * FROM {$this->table} WHERE nome LIKE ? LIMIT ?,?",
								"GET_BY_ID_REGIONE"=>"SELECT * FROM {$this->table} WHERE id_regione= ? LIMIT ?,?");
	}

	function getByNome($nome, $limit_from='',$limit_to='')
	{
		$nome= $this->sanitize($nome)."%";
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_NOME']);

		$statement->bind_param("sii",$nome,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}

	function getByIdRegione($id, $limit_from='',$limit_to='')
	{
		$id= $this->sanitize($id);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_REGIONE']);
		$statement->bind_param("iii",$id,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}
}
?>
