<?php
require_once("model.php");

class TableSupermercato
{
	public $id;
	public $id_colletta;
	public $id_catena;
	public $indirizzo;
	public $email;
	public $telefono;
	public $contattato;
	public $id_comune;
	public $id_diocesi;
	public $nome;
	public $id_magazzino;
	public $id_area;
	public $confermato;

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
		$this->join_statement= " JOIN supermercati_anagrafica B ON A.id_supermercato=B.id";
		$this->select_statement= "A.id, A.id_supermercato, A.id_colletta, A.id_catena, A.confermato, A.nome, A.id_magazzino, A.id_area, B.id_comune, B.id_diocesi, C.nome";
		$this->statements= array(
			"GET_ALL"=>"SELECT * FROM {$this->table} A {$this->join_statement}",
			"GET_BY_ID"=>"SELECT * FROM {$this->table} A {$this->join_statement} WHERE A.id=?",
			"GET_BY_NOME"=>"SELECT * FROM {$this->table} A {$this->join_statement} WHERE A.nome LIKE ? LIMIT ?,?",
			"GET_BY_ID_SUPERMERCATO"=>"SELECT * FROM {$this->table} A {$this->join_statement} WHERE A.id_supermercato= ? LIMIT ?,?",
			"GET_BY_ID_CATENA"=>"SELECT * FROM {$this->table} A {$this->join_statement} WHERE A.id_catena= ? LIMIT ?,?",
			"GET_BY_ID_MAGAZZINO"=>"SELECT * FROM {$this->table} A {$this->join_statement} WHERE A.id_magazzino= ? LIMIT ?,?",
			"GET_BY_ID_AREA"=>"SELECT * FROM {$this->table} A {$this->join_statement} WHERE A.id_area= ? LIMIT ?,?",
			"GET_BY_ID_AREA_NO_LIMITS"=>"SELECT * FROM {$this->table} A {$this->join_statement} WHERE A.id_area= ?",
			"GET_BY_ID_COLLETTA"=>"SELECT * FROM {$this->table} A {$this->join_statement} WHERE A.id_colletta= ? LIMIT ?,?",
			"GET_BY_ID_COMUNE"=>"SELECT * FROM {$this->table} A {$this->join_statement} WHERE B.id_comune= ? LIMIT ?,?");
	}

	function getAll($limit_from=null, $limit_to=null)
	{
		$sql= $this->statements["GET_ALL"];
		if($limit_from!=null)
			$sql.=" LIMIT {$limit_from}";
		if($limit_to!=null)
			$sql.=", {$limit_to}";
		$res= $this->executeStandardQuery($sql);
		return $res;
	}

	function getById($id, $limit_from='',$limit_to='')
    {
        $nome= $this->sanitize($id)."%";
        $statement= $this->connector->connection->prepare($this->statements['GET_BY_ID']);

        $statement->bind_param("s",$id);
        $res= $this->executePreparedQuery($statement);
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

	function getByIdSupermercato($id_supermecato, $limit_from='',$limit_to='')
	{
		$id_supermecato= $this->sanitize($id_supermecato);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_SUPERMERCATO']);

		$statement->bind_param("sii",$id_supermecato,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}

	function getByIdCatena($id_catena, $limit_from='',$limit_to='')
	{
		$id_catena= $this->sanitize($id_catena);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_CATENA']);

		$statement->bind_param("sii",$id_catena,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}
	function getByIdMagazzino($id_magazzino, $limit_from='',$limit_to='')
	{
		$id_magazzino= $this->sanitize($id_magazzino);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_MAGAZZINO']);

		$statement->bind_param("sii",$id_magazzino,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}
	function getByIdArea($id_area, $limit_from='',$limit_to='')
	{
		$id_area= $this->sanitize($id_area);

        if($limit_from!='')
        {
            $statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_AREA']);

            $statement->bind_param("sii",$id_area,$limit_from, $limit_to);
            $res= $this->executePreparedQuery($statement);
        }
        else
        {
            $statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_AREA_NO_LIMITS']);
            $statement->bind_param("s",$id_area);
            $res= $this->executePreparedQuery($statement);
        }
		return $res;
	}
	function getByIdColletta($id_colletta, $limit_from='',$limit_to='')
	{
		$id_colletta= $this->sanitize($id_colletta);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_COLLETTA']);

		$statement->bind_param("sii",$id_colletta,$limit_from, $limit_to);
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
}
?>
