<div class='container'> 
	<a href='add.php?logout=true'><p id='logout'>Logout</p></a>
</div>

<!-- FORM FOR CHANGING ALBUM INFORMATION --> 
<form action="add.php?action=delete" method="post"> 
	<h3> Delete Album: </h3>
	<label for="albumdelete">Album to Delete: </label>
	<select name='albumdelete' id='albumdelete'>
		<?php 
		include_once 'config.php';
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_error) {
			die ("CONNECTION FAILED: ".$mysqli->connect_error);
		}
		$albums = "SELECT * FROM Albums ORDER BY albumName ASC";
		$result = $mysqli->query($albums);
		while ($row = $result->fetch_assoc()) {
			echo "<option value='$row[albumID]'>$row[albumName]</option>";
		}
		?>
	</select><br>

	<input type="submit" name= "submit-delete-album" id="submit-delete-album" value="Submit">
</form>

<!-- FORM FOR CHANGING IMAGE INFORMATION --> 
<form action="add.php?action=delete" method="post"> 
	<h3> Delete Image: </h3>
	<label for="imagedelete">Image to Delete: </label>
	<select name='imagedelete' id='imagedelete'>
		<?php 
		include_once 'config.php';
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_error) {
			die ("CONNECTION FAILED: ".$mysqli->connect_error);
		}
		$albums = "SELECT * FROM Images ORDER BY name ASC";
		$result = $mysqli->query($albums);
		while ($row = $result->fetch_assoc()) {
			echo "<option value='$row[imageID]'>$row[name]</option>";
		}
		?>
	</select><br>
	<input type="submit" name= "submit-delete-image" id='submit-delete-image' value="Submit">
</form>
