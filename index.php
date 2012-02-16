<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
    <head>
        <title>Twitter search</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="css/design.css" />
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="css/calendar.css" />
		<script type="text/javascript" src="calendar.js"></script>
    </head>
	
	<?php
		include("mysql_connection.php");
		
		function secure($string)
		{
			return strip_tags(trim($string));
		}
		
		function createURL()
		{
			$tmp = "";
			
			if(isset($_GET['multiple_words']))
				$tmp .= "&multiple_words=" . $_GET['multiple_words'];
			
			if(isset($_GET['phrase']))
				$tmp .= "&phrase=" . $_GET['phrase'];
				
			if(isset($_GET['any_words']))
				$tmp .= "&any_words=" . $_GET['any_words'];
				
			if(isset($_GET['exclude_words']))
				$tmp .= "&exclude_words=" . $_GET['exclude_words'];
				
			if(isset($_GET['hashtag']))
				$tmp .= "&hashtag=" . $_GET['hashtag'];
				
			if(isset($_GET['from_person']))
				$tmp .= "&from_person=" . $_GET['from_person'];
				
			if(isset($_GET['to_person']))
				$tmp .= "&to_person=" . $_GET['to_person'];
				
			if(isset($_GET['referencing_person']))
				$tmp .= "&referencing_person=" . $_GET['referencing_person'];
				
			if(isset($_GET['since_date']))
				$tmp .= "&since_date=" . $_GET['since_date'];
				
			if(isset($_GET['until_date']))
				$tmp .= "&until_date=" . $_GET['until_date'];
			
			return $tmp;
		}
	
		// Number of results per page
		$Per_Page = 100;
		$Result_Set = 0;
		// field indexed for words research
		$msg = "tweet_text";
		
		// QUERY
		$query_1 = "SELECT * FROM `tweet` WHERE ";
		
		// get values from form
		$types = array();
		
		// WORDS

		if (!empty($_GET['multiple_words']) || !empty($_GET['phrase']) || !empty($_GET['any_words']) || !empty($_GET['exclude_words']))
		{
			$match = "MATCH($msg) AGAINST ('";
		
		//All of these words
		if (isset($_GET['multiple_words']) && !empty($_GET['multiple_words']))
		 {
			$words = explode (" ", $_GET['multiple_words']);
			$count = count($words);				
			$temp = "";
			for ($i = 0; $i < $count; $i++) {
				$temp .= "+$words[$i] ";
			}
			$match .= $temp;
		 }
		 
		//This exact phrase
		if (isset($_GET['phrase']) && !empty($_GET['phrase']))
 			$match .= "\"".$_GET['phrase']."\" ";
		 
		
		//Any of these words
		if (isset($_GET['any_words']) && !empty($_GET['any_words']))
		 {
			$words = explode (" ", $_GET['any_words']);
			$count = count($words);
			$temp = "";
			for ($i = 0; $i < $count; $i++) {
				$temp .= "$words[$i] ";
			}
			$match .= $temp;
		 }
			
		//None of these words
		if (isset($_GET['exclude_words']) && !empty($_GET['exclude_words']))
		 {
			$words = explode (" ", $_GET['exclude_words']);
			$count = count($words);
			$temp = "";
			for ($i = 0; $i < $count; $i++) {
				$temp .= "-$words[$i] ";
			}
			$match .= $temp; 
		 }

	
		 $match .= "' IN BOOLEAN MODE)";
		 
		 $types[] = $match;
		 
		}
		
		// This Hashtag
	 	if (isset($_GET['hashtag']) && !empty($_GET['hashtag']))
 			// $types[] = "MATCH($msg) AGAINST('\"#".$_GET['hashtag'] . "\"' IN BOOLEAN MODE) ";
			$types[] = "MATCH ($msg) AGAINST('\"". $_GET['hashtag'] . "\"') AND `tweet_text` LIKE \"%#" . $_GET['hashtag'] . "%\"";

		
		// PEOPLE
		if (isset($_GET['from_person']) && !empty($_GET['from_person']))
			$types[] = "tweet_user_login='".$_GET['from_person']."'";
		if (isset($_GET['to_person']) && !empty($_GET['to_person']))
			$types[] = "MATCH($msg) AGAINST('@".$_GET['to_person']."') AND `tweet_text` LIKE \"%@" . $_GET['to_person'] . "%\" ";
		if (isset($_GET['referencing_person']) && !empty($_GET['referencing_person']))
			$types[] = "MATCH($msg) AGAINST('@".$_GET['referencing_person']."') AND `tweet_text` LIKE \"%@" . $_GET['referencing_person'] . "%\" ";
			
			
		// DATES
		if (isset($_GET['since_date']) && !empty($_GET['since_date']))
		{
			//Between
			if (isset($_GET['until_date']) && !empty($_GET['until_date']))
			{
				$types[] = "`tweet_date` BETWEEN '".$_GET['since_date']."' AND '".$_GET['until_date']."' ";
			}
			else // Since
			{
				$types[] = "`tweet_date` BETWEEN '".$_GET['since_date']."' AND CURDATE() ";
			}
		}
		if ((isset($_GET['until_date']) && !empty($_GET['until_date'])) && empty($_GET['since_date']))
		{
			$types[] = "`tweet_date` < '".$_GET['until_date'] . "'";
		}
		
		$query_2 = implode(" AND ", $types);
		$query = $query_1 . $query_2 . " ORDER BY tweet_date DESC ";

		// LIMIT for Pagination
		if (empty($_GET['Result_Set']))
		{
			$Result_Set = 0;
			$query .= " LIMIT $Result_Set, $Per_Page";
		} else {
			$Result_Set = $_GET['Result_Set'];
			$query .= " LIMIT $Result_Set, $Per_Page";
		}

		//$query .= " LIMIT 0, 30";
		
		// ORDER BY ?
		echo $query;
		
 
	?>
	
    <body>
	<div id="global">
	<div id="bandeau">
	    
		<table width="100%" style="text-align: center">
		<tr>
		<th width="40%">Words</th>
		<th width="30%">People</th>
		<th width="30%">Date</th>
		</tr>
		<tr>
		<form action="index.php" method="GET">
		<td width="40%">
		<p class="double">
        <label for="multiple_words">All of these words</label>
		<input type="text" name="multiple_words" id="multiple_words" value="<?php if(isset($_GET['multiple_words'])) { echo htmlentities($_GET['multiple_words']);}?>" /><br />
        <label for="phrase">This exact phrase</label> 
		<input type="text" name="phrase" id="phrase" value="<?php if(isset($_GET['phrase'])) { echo htmlentities($_GET['phrase']);}?>" /><br />
        <label for="any_words">Any of these words</label>
		<input type="text" name="any_words" id="any_words" value="<?php if(isset($_GET['any_words'])) { echo htmlentities($_GET['any_words']);}?>" /><br />
        <label for="exclude_words">None of these words</label>  
		<input type="text" name="exclude_words" id="exclude_words" value="<?php if(isset($_GET['exclude_words'])) { echo htmlentities($_GET['exclude_words']);}?>" /><br />
        <label for="hashtag">This hashtag</label>
		<input type="text" name="hashtag" id="hashtag" value="<?php if(isset($_GET['hashtag'])) { echo htmlentities($_GET['hashtag']);}?>" /><br />
		</p>
		</td>
		
		<td width="30%">
		<p class="double">
		<label for="from_person">From this person</label>
		<input type="text" name="from_person" id="from_person" value="<?php if(isset($_GET['from_person'])) { echo htmlentities($_GET['from_person']);}?>" /><br />
        <label for="to_person">To this person</label>
		<input type="text" name="to_person" id="to_person" value="<?php if(isset($_GET['to_person'])) { echo htmlentities($_GET['to_person']);}?>" /><br />
        <label for="referencing_person">Referencing this person</label>
		<input type="text" name="referencing_person" id="referencing_person" value="<?php if(isset($_GET['referencing_person'])) { echo htmlentities($_GET['referencing_person']);}?>" /><br />
		</p>
		</td>
		
		<td width="30%">
		(yyyy-mm-dd)
		<p class="double">
		<label for="since_date">Since this date</label>
		<input type="text" name="since_date" id="since_date" value="<?php if(isset($_GET['since_date'])) { echo htmlentities($_GET['since_date']);}?>" />
		<script type="text/javascript">calendar.set("since_date");</script><br />
		<label for="until_date">Until this date</label>
		<input type="text" name="until_date" id="until_date" value="<?php if(isset($_GET['until_date'])) { echo htmlentities($_GET['until_date']);}?>" />
		<script type="text/javascript">calendar.set("until_date");</script><br />
		</p>
		</td>
		
		</tr>
		<tr>
		<td>
			<p>
			<input type="submit" value="Search" />
			<input type="button" OnClick="location.href='index.php'" value="Reset" />
			</p>
			   </form>
		</td>
		</tr>
		</table>

	
	</div>
	
	<div id="central">
	<div id="menu">
	<center><b><u>Statistics</b></u></center>
	<hr>
	<br />
	<u>Total results</u>
	<?php
		if (isset($_GET['multiple_words']) || isset($_GET['since_date']))
		{
			$query_tmp = $query_1 . $query_2;
			// $query2 = "SELECT * FROM `tweet` WHERE MATCH(tweet_text) AGAINST('hard') AND `tweet_date` > SUBDATE(SYSDATE(), INTERVAL 7 DAY)"; 
			$reponse = mysql_query($query_tmp) or die(mysql_error());
			$Total = mysql_num_rows($reponse);
			echo "<br /><br />" . $Total . " tweets"; 
		 }
		 
	?>
	<hr>
	<br />
	<br />
	<u>Last 24 hours</u>
	<?php
	/*	if (isset($_GET['multiple_words']) || isset($_GET['since_date']))
			{
				$query_tmp = $query_1 . "`tweet_date` > SUBDATE(SYSDATE(), INTERVAL 1 DAY) AND " . $query_2;
				// $query2 = "SELECT * FROM `tweet` WHERE MATCH(tweet_text) AGAINST('hard') AND `tweet_date` > SUBDATE(SYSDATE(), INTERVAL 7 DAY)"; 
				$reponse = mysql_query($query_tmp) or die(mysql_error());
				$reponse = mysql_num_rows($reponse);
				echo "<br /><br />" . $reponse . " tweets";
			}

	
	//SELECT* FROM news WHERE date > SUBDATE(SYSDATE(), INTERVAL 1 MONTH)
	*/
	?>
	<hr>
	<br />
	<br />
	<u>Last 7 days</u>
	<?php
		/*	if (isset($_GET['multiple_words']) || isset($_GET['since_date']))
			{
				$query_tmp = $query_1 . "`tweet_date` > SUBDATE(SYSDATE(), INTERVAL 7 DAY) AND " . $query_2;
				// $query2 = "SELECT * FROM `tweet` WHERE MATCH(tweet_text) AGAINST('hard') AND `tweet_date` > SUBDATE(SYSDATE(), INTERVAL 7 DAY)";
				$reponse = mysql_query($query_tmp) or die(mysql_error());
				$reponse = mysql_num_rows($reponse);
				echo "<br /><br />" . $reponse . " tweets";
			}
	// SELECT* FROM news WHERE date > SUBDATE(SYSDATE(), INTERVAL 1 MONTH)
	*/
	?>
	<hr>
	<br />
	</div>
	
	<div id="contenu">
	<?php
		if (isset($_GET['multiple_words']) || isset($_GET['since_date']))
		{
			$reponse = mysql_query($query) or die(mysql_error());
			$Total = mysql_num_rows($reponse);
		
			if ($Total == 0)
				echo "No results found.";
		
			// echo $Total;    
			// Affichage de chaque message (toutes les données sont protégées par htmlspecialchars)
			while ($donnees = mysql_fetch_array($reponse))
			{
				?>
				<p>
				<?php
								if ($donnees['tweet_retweet'] != NULL)
					echo "<img src=\"retweet.png\" width=\"20px\" alt=\"Retweet\" />";
				?>
				<font color="#2276BB"><?php echo htmlspecialchars($donnees['tweet_user_login']); ?></font>
				(<font color="#999"><?php echo htmlspecialchars($donnees['tweet_date']); ?></font>) : <?php echo $donnees['tweet_text']; ?> </p>
				<hr color="#999">
				<?php
			}
			
			
			// Create Next / Prev Links for Pagination
			if ($Total >= $Per_Page)
			{
				if ($Result_Set >= $Per_Page)
				{
					$Res1 = $Result_Set - $Per_Page;
					echo "<A HREF=\"index.php?Result_Set=$Res1" . createURL() . "\"> << Previous Page </A> ";
				}
				echo "   -   ";
				if ($Total >= $Per_Page)
				{
					$Res1 = $Result_Set + $Per_Page;
					echo " <A HREF=\"index.php?Result_Set=$Res1" . createURL() . "\"> Next Page >> </A>";
				}
			}
		}

		mysql_close();
	?>
	
	</div>
	</div>

	<div id="piedpage"></div>
	</div>
</body>
</html>