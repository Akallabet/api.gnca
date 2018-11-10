<?php
require_once("model.php");

class TableSupermercato
{
	public $id;
	public $nome;
	public $id_catena;
	public $indirizzo;
	public $comune;
	public $provincia;

	public function __construct()
	{

	}
}

class Supermercati extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'supermercati';
		$this->table_model= "TableSupermercato";
		$this->statements= array("GET_BY_NOME"=>"SELECT * FROM {$this->table} WHERE nome LIKE ? LIMIT ?,?",
								"GET_MAX_ID_SUPERMERCATO"=>"SELECT MAX(id_supermercato) as max FROM {$this->table}",
								"GET_BY_ID_CATENA"=>"SELECT * FROM {$this->table} WHERE id_catena= ? LIMIT ?,?",
								"GET_BY_COMUNE"=>"SELECT * FROM {$this->table} WHERE comune LIKE ? LIMIT ?,?",
								"GET"=>"SELECT * FROM {$this->table} WHERE id_colletta= ?",
								"GET_BY_ID_AREA"=>"SELECT * FROM {$this->table} WHERE id_area = ? AND id_colletta= ? LIMIT ?,?",
								"GET_BY_ID_AREA_NO_LIMITS"=>"SELECT * FROM {$this->table} WHERE id_area = ? AND id_colletta= ?",
								"GET_BY_PROVINCIA"=>"SELECT * FROM {$this->table} WHERE provincia= ? LIMIT ?,?");
	}

	function maxIdSupermercato()
	{
		$res= $this->executeStandardQuery($this->statements['GET_MAX_ID_SUPERMERCATO']);
		return $res;
	}

	function insert($parameters, $duplicate=false) {
		$supermercato = parent::insert($parameters, $duplicate);
		if (array_key_exists('id', $supermercato)) {
			$this->executeStandardQuery("INSERT INTO supermercati_aree (id_area, id_supermercato) SELECT id_area, id FROM supermercati WHERE id=".$supermercato['id']);
		}
		return $supermercato;
	}

	function get($params)
	{
		$id_colletta = $this->sanitize($params->id_colletta);
		$statement= $this->connector->connection->prepare($this->statements['GET']);
		$statement->bind_param("ss", $id_colletta);
		$res= $this->executePreparedQuery($statement);
    
		for ($i=0; $i < count($res); $i++) {
			$res[$i]->id = $res[$i]->id."";
			$res[$i]->id_catena = $res[$i]->id_catena."";
			$res[$i]->id_area = $res[$i]->id_area."";
			$res[$i]->id_supermercato = $res[$i]->id_supermercato."";
			$res[$i]->id_colletta = $res[$i]->id_colletta."";
			$res[$i]->id_comune = $res[$i]->id_comune."";
			$res[$i]->id_provincia = $res[$i]->id_provincia."";
		}
		return $res;
	}

	function getByIdArea($params, $limit_from='',$limit_to='')
	{
		$id_area= $this->sanitize($params->id_area);
		$id_colletta = $this->sanitize($params->id_colletta);
        if($limit_from!='')
        {
            $statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_AREA']);

            $statement->bind_param("ssii",$id_area, $id_colletta, $limit_from, $limit_to);
            $res= $this->executePreparedQuery($statement);
        }
        else
        {
            $statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_AREA_NO_LIMITS']);
            $statement->bind_param("ss",$id_area, $id_colletta);
            $res= $this->executePreparedQuery($statement);
        }
		for ($i=0; $i < count($res); $i++) {
			$res[$i]->id = $res[$i]->id."";
			$res[$i]->id_catena = $res[$i]->id_catena."";
			$res[$i]->id_area = $res[$i]->id_area."";
			$res[$i]->id_supermercato = $res[$i]->id_supermercato."";
			$res[$i]->id_colletta = $res[$i]->id_colletta."";
			$res[$i]->id_comune = $res[$i]->id_comune."";
			$res[$i]->id_provincia = $res[$i]->id_provincia."";
		}
		return $res;
	}

	function getByNome($nome, $limit_from='',$limit_to='')
	{
		$nome= $this->sanitize($nome)."%";
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_NOME']);

		$statement->bind_param("sii",$nome,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}

	function getByComune($comune, $limit_from='',$limit_to='')
	{
		$comune= $this->sanitize($comune)."%";
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_COMUNE']);

		$statement->bind_param("sii",$comune,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}

	function getByProvincia($provincia, $limit_from='',$limit_to='')
	{
		$provincia= $this->sanitize($provincia);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_COMUNE']);

		$statement->bind_param("sii",$provincia,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}

	function getByIdCatena($id, $limit_from='',$limit_to='')
	{
		$id= $this->sanitize($id);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_CATENA']);
		$statement->bind_param("iii",$id,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}
}
?>
