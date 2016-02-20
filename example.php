<?php
require_once('MysqlBackup.php');


//prendre en compte tous les paramètres
/*
$config = array(
					'host' => 'YOUR_HOST',
					'user' => 'YOUR_USER',
					'password' => 'YOUR_PASSWORD',
					'database' => 'YOUR_DATABASE',
					'debug' => false,
				);
				
				
$obj = new MysqlBackup($config);

$obj->backup();
*/

//ou le paramètre database si vous êtes en local

$obj = new MysqlBackup(array('database' => 'MY_LOCAL_DATABASE'));

//activez ou desactivez le debug par defaut le debug est activé

//desactivez
$obj->debug(false);

//reactivez 
$obj->debug(true);

$obj->backup();
