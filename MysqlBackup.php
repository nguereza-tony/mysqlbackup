<?php
	
	
	if(!in_array('mysql',get_loaded_extensions())){
		die('Vous devez activer l\'extension Mysql ou utiliser une version de PHP qui supporte cet extension');
	}
	
	
class MysqlBackup{
	
	
	/*********************  properties ******************************/
	private $host = null;
	private $user = null;
	private $password = null;
	private $database = null;
	private $path = null;
	private $link = null;
	private $debug = true;
	
	/*********************  constructor ******************************/
	public function __construct(array $config = array()){
		$default = array(
							'Host' => 'localhost',
							'user' => 'root',
							'password' => ''
						);
		$config = array_merge($default, $config);
		
		$this->setHost($config['Host']);
		$this->setUser($config['user']);
		$this->setPassword($config['password']);
		
		
		if(isset($config['path'])){
			$this->setPath($config['path']);
		}
		else{
			$this->setPath('./');
		}
		
		if(isset($config['debug'])){
			$this->debug($config['debug']);
		}
		
		if(!$this->link = @mysql_connect($this->getHost(), $this->getUser(), $this->getPassword())){
			self::errorLog('Impossible de se connecter au serveur : '.mysql_error());
		}
		
		if(isset($config['database'])){
			$this->setDatabase($config['database']);
		}
		
		
	}
	
	public function debug($status = true){
		$this->debug = $status;
	}
	
	
	public function backup(){
		if(!$this->getDatabase()){
			self::errorLog('Vous devez préciser une base de données avant d\'effectuer une sauvegarde.');
		}
		$filename = 'backup_'.$this->getDatabase().'.sql';
		$path = rtrim($this->getPath(),'/');
		$path = $path.'/'.$filename;
		$fp = fopen($path, 'w+');
		$database = $this->getDatabase();
		
		if($this->debug){
			echo "***************** DEBUT DE LA SAUVEGARDE DE LA BASE DE DONNEES $database ***********************<br /><br />";
		}
		$content = "-- ---------------------------------------------------------------\n";
		$content .= "-- -------- Sauvegarde de la base de donnees ".$database." ------------\n";
		$content .= "-- -------- Date ".date('d/m/Y h:i')." ------------\n";
		$content .= "-- -------- Auteur Tony NGUEREZA ------------\n";
		$content .= "-- ---------------------------------------------------------------\n\n";
		
		$content .= "DROP DATABASE IF EXISTS $database;\n";
		$content .= "CREATE DATABASE IF NOT EXISTS $database;\n";
		$content .= "USE $database;\n\n";
		
		$sql_tables = mysql_query("SHOW TABLE STATUS");
		
		while($tables = mysql_fetch_array($sql_tables)){
			$current_table = $tables['Name'];
			$content .= "-- ---------------- structure de la table $current_table ------------------\n";
			if($this->debug){
				echo "Structure de la table <b>$current_table</b> ...<br />";
			}
			$content .= "DROP TABLE IF EXISTS $current_table;\n";
			$sql_create_tables = mysql_query("SHOW CREATE TABLE $current_table");
			while($sql_table = mysql_fetch_array($sql_create_tables)){
				$content .= $sql_table['Create Table'].";\n";
				$content .= "-- -----------------------------------------------------------------\n\n\n";
				
				$content .= "-- ---------------- données de la table $current_table ------------------\n";
				if($this->debug){
					echo "Donnees de la table <b>$current_table</b> ...<br />";
				}
				$sql_data_table = mysql_query("SELECT * FROM $current_table");
				while($data_table = mysql_fetch_assoc($sql_data_table)){
					$content .= "INSERT INTO `$current_table` VALUES(";
					$count = count($data_table);
					$i = 1;
					foreach($data_table as $key => $value){
						$str = is_numeric($value)?$value:"\"$value\"";
						$content .= $str;
						if($i != $count){
							$content .= " , ";
						}
						$i++;
					}
					$content .= ");\n";
				}
				$content .= "-- -----------------------------------------------------------------\n\n\n";
			}
		}
		
		if(fwrite($fp, $content)){
			if($this->debug){
				echo "<br />***************** FIN DE LA SAUVEGARDE DE LA BASE DE DONNEES $database **************************<br /><br />";
			}
		}
		else{
			self::errorLog("Erreur lors de la sauvegarde");
		}
		fclose($fp);
	}
	
	
	public static function errorLog($msg){
		if(php_sapi_name() != 'cli'){
			die($msg);
		}
	}
	
	
	/*********************  getters ******************************/
	public function getHost(){
		return $this->host;
	} 
	
	public function getUser(){
		return $this->user;
	} 
	
	public function getPassword(){
		return $this->password;
	} 
	
	public function getDatabase(){
		return $this->database;
	} 
	
	public function getPath(){
		return $this->path;
	} 
	
	
	
	/*********************  setters ******************************/
	public function setHost($host){
		$this->host = $host;
	} 
	
	public function setUser($user){
		$this->user = $user;
	} 
	
	public function setPassword($password){
		$this->password = $password;
	} 
	
	public function setDatabase($database){
		$this->database = strtolower($database);
		if(!@mysql_select_db($this->getDatabase(), $this->link)){
			self::errorLog('Impossible de selectionner la base de données : '.mysql_error());
		}
	} 
	
	public function setPath($path){
		if(is_dir($path)){
			$this->path = $path;
		}
		else{
			self::errorLog('Chemin d\'accès de la sauvegarde invalide : '.$path);
		}
	} 
	
	
	
}

?>