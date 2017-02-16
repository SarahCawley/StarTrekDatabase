<?php
header( 'Cache-Control: no-store, no-cache, must-revalidate' ); 
header( 'Cache-Control: post-check=0, pre-check=0', false ); 
header( 'Pragma: no-cache' ); 
?>
<?php
//Turn on error reporting
ini_set('display_errors', 'On');
//Connects to the database
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","cawleys-db","N8zPsvQQLymkGPe8","cawleys-db");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<body>
<h1>Star Trek The DataBase</h1>

<?php
//shows the first and last name of all characters in the database
echo'
<fieldset>
<legend>List of characters</legend>
	<table>
		<tr>
			<th>First Name</th>
			<th>Last name</th>
		</tr>';
			
			//calls first and last names from star_character
			if(!($stmt = $mysqli->prepare("SELECT first_name AS 'First Name', 
					last_name AS 'Last Name'
					FROM star_character;"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}

			if(!$stmt->execute()){
				echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}
			if(!$stmt->bind_result($fName, $lName)){
				echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
			}
			while($stmt->fetch()){
			 echo "<tr>\n
			 			<td>\n" . $fName . "\n</td>\n
			 			<td>\n" . $lName . "\n</td>\n
			 		</tr>";
			}
			$stmt->close();
	echo'		
	</table>
</fieldset>
<br>';

//drop down menu with character names. when character is chosen will show:
//first name, last name, birth planet, starting rank, ending rank
//ships character has served on, and series character has appeared on
echo'
<fieldset>
<legend>View details about your favorite character</legend>
	<form method="post" action="startrek.php">
		<select name="Ranking">';
		
		//pull in name and id to create drop down menu
		if(!($stmt = $mysqli->prepare("SELECT character_ID, first_name, last_name FROM star_character"))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($character_ID, $fname, $lname)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		echo '<option>Choose</option>';
		while($stmt->fetch()){

		 echo '<option value=" '. $character_ID . ' "> ' . $fname . " " . $lname .'</option>\n';
		}
		echo '</select>';
		$stmt->close();	
		//submit button
		echo '<p><input type="submit" value="Find Fave" name="Go!" /></p>
	</form>';

		// Check if submit button has been pressed. If so show information for selected character
	if ( isset( $_POST['Go!'] ) ) {
	   	//set return value from form 
   		$Ranking=$_POST['Ranking'];

   		//pull in ranking information
		if(!($stmt = $mysqli->prepare("SELECT first_name AS 'First Name', 
		last_name AS 'Last Name', 
		(select rank_name from rank where rank_id = start_rank) as 'Starting Rank',
		(select rank_name from rank where rank_id = end_rank) as 'Ending Rank',
		birth_place AS 'Birth Place'
		FROM star_character
		WHERE character_ID = " . $Ranking . ";"))){
		echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}

		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($fName, $lName, $startingRank, $endingRank, $birthPlace)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}

		while($stmt->fetch()){
		 	echo "		
		 		<ul>\n Name: " . $fName . " " . $lName . "\n</ul>\n
	 			<ul>\n Starting Rank: " . $startingRank . "\n</ul>\n
	 			<ul>\n Ending Rank: " . $endingRank . "\n</ul>\n
	 			<ul>\n Birth Planet: " . $birthPlace . "\n</ul>\n";
		}
		$stmt->close();

		//get ships served on information
		echo "<ul>Ships served on: ";

		if(!($values = $mysqli->prepare("SELECT ship_name FROM ship
		INNER JOIN ship_character ON ship_character.ship_ID = ship.ship_ID
		WHERE ship_character.character_ID =  $Ranking ;"))){
			echo "Prepare failed: "  . $values->errno . " " . $values->error;
		}
		if(!$values->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$values->bind_result($shipName)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		while($values->fetch()){
		 	echo $shipName . ", ";		
		}

		echo "</ul>";
		
		//get series' appeared on information
		echo "<ul>Series' appeared on: ";

		if(!($values = $mysqli->prepare("SELECT series_name FROM series 
				INNER JOIN series_character ON series_character.series_ID = series.series_ID
				WHERE series_character.character_ID =  $Ranking ;"))){
			echo "Prepare failed: "  . $values->errno . " " . $values->error;
		}
		if(!$values->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$values->bind_result($seriesName)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		while($values->fetch()){
		 	echo $seriesName . ", ";		
		}
		echo "</ul>";
	}
echo'
</fieldset>';

//allows users to add new character. Will only add first name, last name
//and birthplace. other connections will be made later
echo'
<br>
<fieldset>
<legend>Add a new character</legend>	
	<form method="post" action="startrek.php">';
		//input character first name
		echo '<p>First Name:  <INPUT TYPE = "Text" NAME = "firstName"></p> ';

		//input character last name 
		echo' <p>Last Name:  <INPUT TYPE = "Text" NAME = "lastName"> </p>';

		//input birthplace 
		echo '<p>Birth Planet: <INPUT TYPE = "Text" NAME = "birthPlanet"> </p> ';

		//submit button
		echo '<p><input type="submit" value = "Add!" name="Add" /></p>
	</form>';

	//check if button has been clicked
	if ( isset( $_POST['Add'] ) ) {

		$firstName = $_POST['firstName'];
		$lastName = $_POST['lastName'];
		$homePlanet = $_POST['birthPlanet'];

		//insert first, last, birthplace
		if(!($stmt = $mysqli->prepare("INSERT INTO star_character(first_name, last_name, birth_place) 
										VALUES ('$firstName','$lastName' , '$homePlanet');"))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}

		if(!$stmt->execute()){
			echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
		} else {
			echo "<br>Added " . $stmt->affected_rows . " row: " . $firstName . " " . $lastName . 
			"<br>To view changes please refresh the page" ;
		}
		$stmt->close();
	}
echo'			
</fieldset>';

echo'
<fieldset>
<legend>Add more information to a character</legend>
	<form method = "post" action="startrek.php">';
		
	//character drop down
		echo 
		"Choose the character you would like to update: <select name='person'>";
		//pull in name and id to create drop down menu
		if(!($stmt = $mysqli->prepare("SELECT character_ID, first_name, last_name FROM star_character"))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($character_ID, $fname, $lname)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		echo '<option>Choose</option>';
		while($stmt->fetch()){

		 echo '<option value=" '. $character_ID . ' "> ' . $fname . " " . $lname .'</option>\n';
		}
		echo '</select>';
		$stmt->close();	

	//pull down for series
		echo
		'<p>Choose Series: <select name="chooseSeries"></p>';
		//get series names
		if(!($stmt = $mysqli->prepare("SELECT series_ID, series_name FROM series"))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($series_ID, $seriesName)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		echo '<option>Choose</option>';
		while($stmt->fetch()){
		 echo '<option value=" '. $series_ID . ' "> ' . $seriesName.'</option>\n';
		}
		echo'
				<option value = -1>Other</option>\n
				</select>';
		$stmt->close();

		echo'<p>Choose Ship: <select name="chooseShip"></p>';
	//pull down for ship names
		if(!($stmt = $mysqli->prepare("SELECT ship_ID, ship_name FROM ship"))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($ship_ID, $shipName)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		echo '<option>Choose</option>';
		while($stmt->fetch()){
		 echo '<option value=" '. $ship_ID . ' "> ' . $shipName.'</option>\n';
		}
		echo'
				<option value = -1>Other</option>\n
				</select>';
		$stmt->close();

	//dropdown for starting rank
		echo '<p>Starting Rank: <select name="newStartRanking"></p>';
		
		if(!($stmt = $mysqli->prepare("SELECT rank_ID, rank_name FROM rank"))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}

		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($rank_ID, $rankName)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		echo '<option>Choose</option>';
		while($stmt->fetch()){

		 echo '<option value=" '. $rank_ID . ' "> ' . $rankName.'</option>\n';
		}
		echo'
				<option value = -1>Other</option>\n
				</select>';
	
		$stmt->close();	

	//dropdown for ending rank
		echo '<p>Ending Rank: <select name="newEndRanking"></p>';
		
		if(!($stmt = $mysqli->prepare("SELECT rank_ID, rank_name FROM rank"))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}
		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($rank_ID, $rankName)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		echo '<option>Choose</option>';
		while($stmt->fetch()){
		 echo '<option value=" '. $rank_ID . ' "> ' . $rankName.'</option>\n';
		}
		echo'
				<option value = -1>Other</option>\n
				</select>';
		$stmt->close();

		echo '<br><p><input type="submit" value = "Update Information!" name="updateInfo" /></p>	
	</form>';

		//when button is pressed
		if (isset($_POST['updateInfo'])){
			$character = $_POST['person'];
			$series = $_POST['chooseSeries'];
			$ship = $_POST['chooseShip'];
			$startingRank = $_POST['newStartRanking'];
			$endingRank = $_POST['newEndRanking'];

			//add to  series_character
			if($series == -1 ){
				echo'Please enter new Series below then add it to your character!<br>';
			}
			else{
				if(!($stmt = $mysqli->prepare("INSERT INTO series_character(character_ID, series_ID) 
											VALUES ('$character', '$series')"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
				}
				if(!$stmt->execute()){
					echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
				} else {
					echo "<br>Added " . $stmt->affected_rows . " row: " . 
					"<br>To view changes please refresh the page" ;
				}
				$stmt->close();
			}

			//add ship to character
			if($ship == -1){
				echo'Please enter new Ship below then add it to your character!<br>';
			}
			else{
				//add to  ship_character
				if(!($stmt = $mysqli->prepare("INSERT INTO ship_character(character_ID, ship_ID) 
												VALUES ('$character', '$ship')"))){
					echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
				}

				if(!$stmt->execute()){
					echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
				} else {
					echo "<br>Added " . $stmt->affected_rows . " row: " . 
					"<br>To view changes please refresh the page" ;
				}
				$stmt->close();
			}

			//update beginning rank of character
			if($startingRank == -1){
				echo "Please enter new rank below then add it to your character";
			}
			else{
				$stmt = $mysqli->query("UPDATE star_character SET start_rank =  $startingRank WHERE character_ID = $character");				
			}

			//update ending rank of character
			if($endingRank == -1){
				echo "Please enter new rank below then add it to your character";
			}
			else{
				$stmt = $mysqli->query("UPDATE star_character SET end_rank =  $endingRank WHERE character_ID = $character");				
			}
		}
?>
</fieldset>

<fieldset>
<legend>Add a new rank</legend>
		<form method = "post" action="startrek.php">
			<input type="text" name="addNewRank">
			<input type="submit" value="Add New Rank" name="newRankButton" />
		</form>

		<?php
		//when button has been pressed
		if(isset($_POST['newRankButton'])){
			$newRank = $_POST['addNewRank'];
			if(!($stmt = $mysqli->prepare("INSERT INTO rank(rank_name) 
									VALUES ('$newRank')"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->execute()){
				echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
			} else {
				echo "<br>" . $newRank . "Added ";
			}
			$stmt->close();
		}
		?>
</fieldset>

<fieldset>
<legend>Add a new series</legend>
		<form method = "post" action="startrek.php">
			<input type="text" name="addNewSeries">
			<input type="submit" value="Add New Series" name="newSeriesButton" />
		</form>

		<?php
		//when button has been pressed
		if(isset($_POST['newSeriesButton'])){
			echo"THE BUTTON WAS PRESSED!!!";
			$newSeries = $_POST['addNewSeries'];
			if(!($stmt = $mysqli->prepare("INSERT INTO series(series_name) 
									VALUES ('$newSeries')"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->execute()){
				echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
			} else {
				echo "<br>" . $seriesName . "Added ";
			}
			$stmt->close();
		}
		?>
</fieldset>

<fieldset>
<legend>Add a new ship</legend>
		<form method = "post" action="startrek.php">
			<input type="text" name="addNewShip">
			<input type="submit" value="Add New Ship" name="newShipButton" />
		</form>

		<?php
		//check button has been pressed
		if(isset($_POST['newShipButton'])){
			echo"THE BUTTON WAS PRESSED!!!";
			$newShip = $_POST['addNewShip'];
			if(!($stmt = $mysqli->prepare("INSERT INTO ship(ship_name) 
									VALUES ('$newShip')"))){
				echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
			}
			if(!$stmt->execute()){
				echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
			} else {
				echo "<br>" . $newShip . " added ";
			}
			$stmt->close();
		}
		?>
</fieldset>


<fieldset>
<legend>Delete a character</legend>
	<form method="post" action="startrek.php">
		<?php
		//character drop down
		echo 
		"Choose the character you would like to delete: <select name='deleteCharacter'>";
		//pull in name and id to create drop down menu
		if(!($stmt = $mysqli->prepare("SELECT character_ID, first_name, last_name FROM star_character"))){
			echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
		}

		if(!$stmt->execute()){
			echo "Execute failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		if(!$stmt->bind_result($character_ID, $fname, $lname)){
			echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
		echo '<option>Choose</option>';
		while($stmt->fetch()){

		 echo '<option value=" '. $character_ID . ' "> ' . $fname . " " . $lname .'</option>\n';
		}
		echo '</select>';
		$stmt->close();	
		
		echo '<input type="submit" value="Delete Character" name="deleteCharacterButton" />';

		//if button has been pressed
		if (isset($_POST['deleteCharacterButton'])){
				//delete character
				$stmt = $mysqli->prepare("DELETE FROM star_character WHERE character_ID = ?");
				$stmt->bind_param('i', $_POST['deleteCharacter']);
				$stmt->execute(); 
				$stmt->close();	
		}
echo '</fieldset>';




