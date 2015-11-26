<?php
require_once("connection.php");

class Table
{
	public function __construct($par)
	{

	}
}

class Model
{
	protected $connector;
	protected $table_model;
	protected $statements;
	protected $table;

	public function __construct()
	{
		$this->connector= new Connector();
		$this->statements= array("GET_ALL"=>"SELECT * FROM {$this->table}",
								"GET_BY_ID"=>"SELECT * FROM {$this->table} WHERE id=?");
	}

	function insertOrUpdate($parameters)
	{
		$this->insert($parameters, true);
	}
	function insert($parameters, $duplicate=false)
	{
		$values= array();
		foreach ($parameters->values as $key => $value) {
			$value= (is_object($value)) ? get_object_vars($value) : $value;
			foreach ($value as $k => $v) {
				$value[$k]= '"'.$v.'"';
			}
			$parameters->values[$key]= $value;
			//print_r($parameters->values[$key]);
			//$parameters->values[$key]= (!is_array($value)) ? $this->sanitize(get_object_vars($value)) : $this->sanitize($value);
			$values[]= "(".implode(", ", $parameters->values[$key]).")";
		}
		$query="INSERT INTO {$this->table} (".implode(', ',array_keys($parameters->values[0])).") VALUES ".implode(",", $values);
		if($duplicate)
		{
			$query.=" ON DUPLICATE KEY UPDATE";
			$updup= array();
			foreach (array_keys($parameters->values[0]) as $k=> $ud) {
				$updup[]= "{$ud} = VALUES($ud)";
			}
			$query.= " ".implode(", ", $updup);
		}
		//echo $query;
		$res= $this->connector->connection->query($query);
		if($res) return array('result'=>true, 'id'=>$this->connector->connection->insert_id);
		else return array('result'=>false, 'error'=>$this->connector->connection->error);
	}

	function update($parameters)
	{
		$res=null;
		foreach ($parameters->values as $key => $value) {
			$setValues= array();
			foreach ($value as $column => $val) {
				$setValues[]=  $column.'= "'.$val.'"';
			}

			$set_tmp= get_object_vars($parameters->set[$key]);
			$set_tmp_keys= array_keys($set_tmp);

			$str= "UPDATE  {$this->table} SET ";
			$str.= implode(", ", $setValues);
			$str.= " WHERE {$set_tmp_keys[0]} = '{$set_tmp[$set_tmp_keys[0]]}' ";
			//echo $str;
			$res= $this->executeStandardQuery($str);
		}
		if($res) return array('result'=>true);
		else return array('result'=>false, 'error'=>$this->connector->connection->error);
	}

	function delete($parameters)
	{
		//$ids= $this->sanitize($parameters->set);
		$ids= array();
		//print_r($ids);
		foreach ($parameters->set as $key => $id) {
			$ids[]= $id->id;
		}
		$query= "DELETE FROM {$this->table} WHERE id IN(".implode(", ", $ids).")";
		//echo $query;
		$res= $this->connector->connection->query($query);
		if($res) return array('result'=>true);
		else return array('result'=>false, 'error'=>$this->connector->connection->error);
	}

	public function get($values, $limit_from=null, $limit_to=null)
	{
		$values= $this->sanitize((!is_array($values)) ? get_object_vars($values) : $values);
		$str= "SELECT * FROM {$this->table}";
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
			$str= "SELECT * FROM {$this->table} WHERE ".implode(" AND ", $par);
		}
		$res= $this->executeStandardQuery($str);
		return $res;
	}

	public function getMaxValue($maxValue, $values, $limit_from=null, $limit_to=null)
	{
		$values= $this->sanitize((!is_array($values)) ? get_object_vars($values) : $values);
		$str= "SELECT Max({$maxValue}) as max FROM {$this->table}";
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
			$str.= " WHERE ".implode(" AND ", $par);
		}
		$res= $this->executeStandardQuery($str);
		return $res;
	}

	function getAll($limit_from=null, $limit_to=null)
	{
		$sql= "SELECT * FROM {$this->table}";
		if($limit_from!=null)
			$sql.=" LIMIT {$limit_from}";
		if($limit_to!=null)
			$sql.=", {$limit_to}";
		$res= $this->executeStandardQuery($sql);
		return $res;
	}

	function getById($id)
	{
		$id= $this->sanitize($id);
		$statement= $this->connector->connection->prepare("SELECT * FROM {$this->table} WHERE id=?");
		$statement->bind_param("s",$id);
		$res= $this->executePreparedQuery($statement);
		return $res;
	}

	public function executePreparedQuery($stmt)
	{
		$ret= array();
		$stmt->execute();

        $result = $stmt->get_result();
		$stmt->fetch();

		return  $this->getQueryObject($result);
	}

	public function executeStandardQuery($query)
	{
		$ret= array();
		return $this->getQueryObject($this->connector->connection->query($query));
	}

	public function getQueryObject($resource)
	{
		$ret=array();
		if($resource)
		{
		    if(is_bool($resource))
            {
                $ret= $resource;
            }
            else {
                while ($row = $resource->fetch_object($this->table_model))
                {
                    $ret[]= $row;
                }
            }
		}
		return  $ret;
	}

	public function sanitize($p)
	{
		if(is_object($p)) $p= get_object_vars($p);
		if(is_array($p))
		{
			foreach ($p as $key => $value) {
				if(!is_object($value) && !is_array($value))
				{
					$p[$key]= $this->connector->connection->real_escape_string($value);
				}
			}
		}
		else $p= $this->connector->connection->real_escape_string($p);
		return $p;
	}
}

?>
