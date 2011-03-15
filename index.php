<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
        <title>Twitter search</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    </head>
	
	
    <style type="text/css">
    form
    {
        text-align:left;
    }
    </style>
	
	
	
    <body>
    
    <form action="index.php" method="post">
        
		<p>
		<strong>Words </strong> <br />
        <label for="multiple_words">All of these words</label> : <input type="text" name="multiple_words" id="multiple_words" /><br />
        <label for="phrase">This exact phrase</label> :  <input type="text" name="phrase" id="phrase" /><br />
        <label for="any_words">Any of these words</label> :  <input type="text" name="any_words" id="any_words" /><br />
        <label for="exclude_words">None of these words</label> :  <input type="text" name="exclude_words" id="exclude_words" /><br />
        <label for="hashtag">This hashtag</label> :  <input type="text" name="hashtag" id="hashtag" /><br />
		</p>
		
		<p>
		<strong>People </strong> <br />	
		<label for="from_person">From this person</label> : <input type="text" name="from_person" id="from_person" /><br />
        <label for="to_person">To this person</label> :  <input type="text" name="to_person" id="to_person" /><br />
        <label for="referencing_person">Referencing this person</label> :  <input type="text" name="referencing_person" id="referencing_person" /><br />
		</p>
		
		<p>	
		<strong>Places </strong><br />		
		<label for="near_place">Near this place</label> : <input type="text" name="near_place" id="near_place" /><br />		
		</p>
		
		<p>
		<strong>Dates </strong><br />
		<label for="since_date">Since this date</label> : <input type="text" name="since_date" id="since_date" /><br />
		<label for="until_date">Until this date</label> : <input type="text" name="until_date" id="until_date" /><br />
		</p>
				
		<p>
		<input type="submit" value="Search" />
		</p>
    </form>


<?php
// Connexion à la base de données
try
{
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO('mysql:host=jnickersmacpro1.mgnt.stevens-tech.edu;dbname=lsa', 'lsa', 'stigmergy', $pdo_options);
    
    // Récupération des 10 derniers messages
    //$reponse = $bdd->query('SELECT pseudo, message FROM ta daronne ORDER BY ID DESC LIMIT 0, 10');
    $reponse = $bdd->query('SELECT * FROM `tweet` LIMIT 0, 30');
    
    // Affichage de chaque message (toutes les données sont protégées par htmlspecialchars)
    while ($donnees = $reponse->fetch())
    {
        echo '<p><strong>' . htmlspecialchars($donnees['tweet_id']) . '</strong> : ' . htmlspecialchars($donnees['tweet_text']) . '</p>';
    }
    
    $reponse->closeCursor();
}
catch(Exception $e)
{
    die('Erreur : '.$e->getMessage());
}

?>
</body>
</html>