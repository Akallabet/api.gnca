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
		$str= "SELECT * FROM {$this->table1}";
		if(count($values)>0)
		{
			$par= Array();
			foreach ($values as $key => $value) {
				if(is_object($value))
				{
					$value= get_object_vars($value);
					$keys= array_keys($value);
					if($keys[0]=='IN')
					{
						$par[]= "{$key} IN (".implode(', ', $value[$keys[0]]).")";
					}
				}
				else
				{
					$par[]= "{$key} = {$value}";
				}
			}
			$str= "SELECT * FROM {$this->table1} WHERE ".implode(" AND ", $par);
		}
		$res= $this->executeStandardQuery($str);

		if(count($res) > 0)
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
