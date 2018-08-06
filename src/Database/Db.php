<?php

class DB
{
	const USERNAME = 'root';
	const PASSWORD = 'root';
	const HOST     = 'localhost';
	const DATABASE = 'fishing_booker';

	protected $conn;

	public function __construct()
	{
		try{
			$conn = new PDO(sprintf('mysql:host=%s;dbname=%s', self::HOST, self::DATABASE), self::USERNAME, self::PASSWORD);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->conn = $conn;
		}
		catch(Exception $e){
			echo $e->getMessage();
			exit;
		}
	}
}