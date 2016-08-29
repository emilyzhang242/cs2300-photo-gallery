<div class='container'> 
	<a href='add.php?logout=true'><p id='logout'>Logout</p></a>
</div>

<form action="add.php" method="post"> 
	<h3> Add Album to Site: </h3>
	<label for="album">Album Name*: </label>
	<input type="text" id='album' name="album" placeholder="Album Name"><br>
	<label for='album-des'>Album Description: </label><br>
	<textarea name='album-des' id='album-des' rows='4' cols='50'></textarea><br>
	<input type="submit" name= "submit-album" value="Submit">
</form>

<form action="add.php" method="post" enctype="multipart/form-data"> 
	<h3> Add Image to Site: </h3>
	<label for="image" >Image Name*: </label>
	<input type="text" id="image" name="image" placeholder="Image Name"><br>
	<label >Albums (Select Any #): </label><br>
	<?php 

	require_once 'config.php';

	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_error) {
		die ("CONNECTION FAILED: ".$mysqli->connect_error);
	}
	$result = $mysqli->query("SELECT * FROM Albums");
	while($row = $result->fetch_assoc()) {
		echo" <input type='checkbox' name='album-img[]' value='$row[albumID]'>$row[albumName]";
	}
	?>
	<br>
	<label for="credit" >Credit*: </label>
	<input type="text" id="credit" name="credit" placeholder="Credit given where credit is due"><br>
	<label for="caption" >Caption: </label><br>
	<textarea rows='4' cols='50' id="caption" name="caption"></textarea><br>
	<input type="file" name="file" id="file"><br>
	<input type="submit" name="submit-image" value="Submit">
</form>
