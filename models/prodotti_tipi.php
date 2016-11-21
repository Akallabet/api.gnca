<?php
require_once("model.php");

class TableProdottoTipo
{
	public $id;
  public $id_colletta;
	public $nome;
	public $ordine;

	public function __construct()
	{

	}
}

class ProdottiTipi extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'prodotti_tipi';
		$this->table_model= "TableProdottoTipo";
		$this->statements= array("GET_BY_ID_COLLETTA"=>"SELECT * FROM {$this->table} A WHERE A.id_colletta= ?");
	}


    function getByIdColletta($id_colletta)
    {
        $id_colletta= $this->sanitize($id_colletta);
        $statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_COLLETTA']);
        $statement->bind_param("sii",$id_colletta);
        $res= $this->executePreparedQuery($statement);
        return $res;
    }
}
?>
