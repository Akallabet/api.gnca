<?php
require_once("model.php");

class TableReport
{
	public $id;
	public $nome;
	public $id_catena;
	public $indirizzo;
	public $comune;
	public $provincia;
	public $prodotti;

	public function __construct()
	{

	}
}

class Report extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table1= 'supermercati';
		$this->table2= 'prodotti';
		$this->table_model= "TableReport";
	}

	public function get($values, $limit_from=null, $limit_to=null)
	{
		$values= $this->sanitize((!is_array($values)) ? get_object_vars($values) : $values);
		$str = "SELECT  A.id, A.id_supermercato, A.id_colletta, A.id_catena, A.indirizzo, A.id_comune, A.id_provincia, A.id_diocesi, A.nome, A.id_magazzino, A.id_area FROM {$this->table1} A";
		if (array_key_exists('id_area', $values)) {
			$str.=" JOIN `supermercati_aree` B ON B.id_supermercato = A.id WHERE B.id_area = ".$values['id_area']." AND A.id_colletta=".$values['id_colletta'];
		} else {
			$str.= " WHERE A.id_colletta=".$values['id_colletta'];
		}
		if (array_key_exists('id_comune', $values)) {
			$str.= " AND A.id_comune=".$values['id_comune'];
		}
		if (array_key_exists('id_provincia', $values)) {
			$str.= " AND A.id_provincia=".$values['id_provincia'];
		}
		$res= $this->executeStandardQuery($str);

		if(count($res>0))
		{
			foreach ($res as $key => $sup) {
				$str= "SELECT SUM(kg) AS Kg, SUM(scatole) AS scatole, prodotto FROM {$this->table2} ";
				$str.=" WHERE id_supermercato= '{$sup->id}' GROUP BY prodotto";
				$res[$key]->prodotti= $this->executeStandardQuery($str);
			}
		}
		return $res;
	}
}
?>
