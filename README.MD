
Vous devez avoir une version de PHP inférieur à 7 car la classe utilise les fonctions
de l'extension mysql supprimé à partir de PHP 7.0.0



 1. Téléchargez le zip contenant la classe MysqlBackup

 
 2. Decompressez le zip dans votre repertoire web puis modifiez
 le fichier example.php avec le paramètre de votre choix.
 
 
 3. Ouvrez votre navigateur et allez à l'adresse http://votre_site/MysqlBackup/example.php
 si tout va bien un fichier de sauvegarde au nom de backup_[nom_base_de_donnees].sql
 est créé dans le chemin specifié par le paramètre path. par défaut path est le dossier contenant
 les fichiers MysqlBackup.php et example.php
 
 
 si vous voulez sauvegarder plusieurs base de donnees en même temps:
 
 <?php
 require_once("MysqlBackup.php");
 
 $database = array("db1","db2","dbn");

$config = array(
					'host' => 'YOUR_HOST',
					'user' => 'YOUR_USER',
					'password' => 'YOUR_PASSWORD',
					'debug' => false,
				);
				
				
$obj = new MysqlBackup($config);

foreach($database as $db){



$obj->setDatabase($db);

//desactivez
$obj->debug(false);



//reactivez 
$obj->debug(true);

$obj->backup();
}
?>