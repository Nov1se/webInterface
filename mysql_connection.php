<?php
// Connexion à la base de données
try
{
   // $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    // $bdd = new PDO('mysql:host=jnickersmacpro1.mgnt.stevens-tech.edu;dbname=lsa', 'lsa', 'stigmergy', $pdo_options);
    
	mysql_connect("jnickersmacpro1.mgnt.stevens-tech.edu", "web", "stevens");
	mysql_query("SET NAMES 'utf8'");
	mysql_select_db("newtweets");
	
}
catch(Exception $e)
{
    die('Erreur : '.$e->getMessage());
}

?>