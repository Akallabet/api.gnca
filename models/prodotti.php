<?php
require_once("model.php");

class TableProdotto
{
	public $id;
    public $id_supermercato;
	public $prodotto;
	public $kg;
	public $scatole;
	public $carico;
	public $id_user;
	public $ultima_modifica;

	public function __construct()
	{

	}
}

class Prodotti extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'prodotti';
		$this->table_model= "TableProdotto";
		$this->statements= array("GET_BY_CARICO"=>"SELECT * FROM {$this->table} A WHERE A.carico= ? LIMIT ?,?",
                                "GET_BY_ID_SUPERMERCATO"=>"SELECT * FROM {$this->table} A WHERE A.id_supermercato= ? LIMIT ?,?",
                                "GET_BY_ID_SUPERMERCATO_NO_LIMITS"=>"SELECT * FROM {$this->table} A WHERE A.id_supermercato= ?",
                                "GET_BY_PRODOTTO"=>"SELECT * FROM {$this->table} WHERE A.prodotto= ? LIMIT ?,?",
                                "GET_BY_ID_USER"=>"SELECT * FROM {$this->table} WHERE A.id_user LIKE ? LIMIT ?,?");
	}

    function getByIdSupermercato($id_supermercato, $limit_from='', $limit_to='')
    {
        $id_supermercato= $this->sanitize($id_supermercato);
        if($limit_from=='')
        {
            $statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_SUPERMERCATO_NO_LIMITS']);
            $statement->bind_param("s",$id_supermercato);
        }
        else
        {
            $statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_SUPERMERCATO']);
            $statement->bind_param("sii",$id_supermercato,$limit_from, $limit_to);
        }
        $res= $this->executePreparedQuery($statement);
        return $res;
    }
	function getByCarico($carico, $limit_from='',$limit_to='')
	{
		$carico= $this->sanitize($carico);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_CARICO']);

		$statement->bind_param("sii",$carico,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}
	function getByIdUser($id_user, $limit_from='',$limit_to='')
	{
		$id_user= $this->sanitize($id_user);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_USER']);

		$statement->bind_param("sii",$id_user,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}
	function getByProdotto($prodotto, $limit_from='',$limit_to='')
	{
		$prodotto= $this->sanitize($prodotto);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_PRODOTTO']);

		$statement->bind_param("sii",$prodotto,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}
}
?>
