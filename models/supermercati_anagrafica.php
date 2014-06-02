<?php
require_once("model.php");

class TableSupermercatoAnagrafica
{
	public $id;
	public $indirizzo;
	public $id_comune;
	public $id_diocesi;

	public function __construct()
	{

	}
}

class SupermercatiAnagrafica extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'supermercati_anagrafica';
		$this->table_model= "TableSupermercatoAnagrafica";
		$this->statements= array("GET_BY_INDIRIZZO"=>"SELECT * FROM {$this->table} WHERE indirizzo LIKE ? LIMIT ?,?",
								"GET_BY_ID_COMUNE"=>"SELECT * FROM {$this->table} WHERE id_comune= ? LIMIT ?,?",
								"GET_BY_ID_DIOCESI"=>"SELECT * FROM {$this->table} WHERE id_diocesi= ? LIMIT ?,?");
	}

	function getByIndirizzo($indirizzo, $limit_from='',$limit_to='')
	{
		$indirizzo= $this->sanitize($indirizzo)."%";
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_INDIRIZZO']);

		$statement->bind_param("sii",$indirizzo,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}

	function getByIdComune($id_comune, $limit_from='',$limit_to='')
	{
		$id_comune= $this->sanitize($id_comune);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_COMUNE']);

		$statement->bind_param("sii",$id_comune,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}

	function getByIdDiocesi($id_diocesi, $limit_from='',$limit_to='')
	{
		$id_diocesi= $this->sanitize($id_diocesi);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_DIOCESI']);

		$statement->bind_param("sii",$id_diocesi,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}
}
?>
