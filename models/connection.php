<?php

class Connector
{
	private $DB_NAME = 'stracolletta_alimentare';
	//local
	private $DB_HOST = 'localhost';
	private $DB_USER = 'colletta';
	private $DB_PASS = '171882109';

	//remote
	//private $DB_HOST = '192.186.204.169';
	//private $DB_USER = 'gnca';
	//private $DB_PASS = 'gnca2013';
	//public $connection;

	function Connector()
	{
		 $this->connect();
	}

	function connect()
	{
		$mysqli= new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
		$mysqli->set_charset('utf8');
		if (mysqli_connect_errno())
			return mysqli_connect_errno();
		else return $this->connection= $mysqli;
	}

	function disconnect()
	{
		mysqli_close($this->connection);
	}

	function getConnection() {return $this->connection;}
}

?>
