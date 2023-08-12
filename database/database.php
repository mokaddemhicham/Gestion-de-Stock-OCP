<?php

	// la connexion avec BD en utilisant PDO

	class Database{
		private static $dbhost = "localhost";
		private static $dbname = "gestion_de_stock_v2";
		private static $dbusername = "root";
		private static $dbpassword = "";
		
		private static $connection = null;

		public static function connect(){
			if(self::$connection == null){
				try{
					self::$connection = new PDO("mysql:host=". self::$dbhost . ";dbname=" . self::$dbname, self::$dbusername, self::$dbpassword);
				}
				catch(PDOException $e){
					die("Message : " . $e->getMessage());
				}
			}
			return self::$connection;
		}

		public static function disconnect(){
			return self::$connection = null;
		}
	}

?>