<?php
require_once("model.php");

class ComuniTable
{
	public $id;
	public $nome;
	public $provincia;

	public function __construct()
	{

	}
}

class Comuni extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'comuni';
		$this->table_model= "ComuniTable";
		$this->join_statement= " JOIN province B ON A.id_provincia=B.id";
		$this->select_statement= "A.id, A.nome, A.id_provincia, B.nome as provincia";
		$this->statements= array(
			"GET_ALL"=>"SELECT {$this->select_statement} FROM {$this->table} A {$this->join_statement}",
			"GET_BY_ID"=>"SELECT $this->select_statement FROM {$this->table} A {$this->join_statement} WHERE A.id=?",
			"GET_BY_NOME"=>"SELECT $this->select_statement FROM {$this->table} WHERE A.nome LIKE ? LIMIT ?,?",
			"GET_BY_ID_PROVINCIA"=>"SELECT $this->select_statement FROM {$this->table} WHERE A.id_provincia= ? LIMIT ?,?"
			);
	}

	function get($values, $limit_from=null, $limit_to=null)
	{
		$values= $this->sanitize((!is_array($values)) ? get_object_vars($values) : $values);
		$str= "SELECT {$this->select_statement} FROM {$this->table} A {$this->join_statement}";
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
					$par[]= "A.{$key} = {$value}";
			}
			$str= "SELECT * FROM {$this->table} WHERE ".implode(" AND ", $par);
		}
		$res= $this->executeStandardQuery($str);
		return $res;
	}

	function getAll($limit_from=null, $limit_to=null, $json=false)
	{

		/*
	    if($json)
        {
            $string = file_get_contents("resources/comuni.json");
            $tmp=json_decode($string);

            if($limit_from)
            {
                $i=$limit_from;
                while($i < $limit_to && $i<count($tmp)) {
                    $res[]= $tmp[$i];
                    $i++;
                }
            }
            else $res= $tmp;*
        }
        else {*/
            $sql= $this->statements["GET_ALL"];
            if($limit_from!=null)
                $sql.=" LIMIT {$limit_from}";
            if($limit_to!=null)
                $sql.=", {$limit_to}";
            $res= $this->executeStandardQuery($sql);
        //}

		return $res;
	}

	function getById($id)
	{
		$id= $this->sanitize($id);
		$statement= $this->connector->connection->prepare($this->statements["GET_BY_ID"]);
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

	function getByIdProvincia($id, $limit_from='',$limit_to='')
	{
		$id= $this->sanitize($id);
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_ID_PROVINCIA']);
		$statement->bind_param("iii",$id,$limit_from, $limit_to);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}
}
?>
