<div class='container'> 
	<a href='add.php?logout=true'><p id='logout'>Logout</p></a>
</div>

<!-- FORM FOR CHANGING ALBUM INFORMATION --> 
<form action="add.php?action=edit" method="post"> 
	<h3> Update Album: </h3>
	<p> If you leave the field blank, it remains the same. </p>
	<label for="albumedit">Album to Update*: </label>
	<select name='albumedit' id='albumedit'>
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
	<label for="newalbumname">New Album Name: </label>
	<input type="text" id='newalbumname' name="newalbumname" placeholder="Album Name"><br>
	<label for="albumcover">New Album Cover Image: </label>
	<select name='albumcover'>
		<option value="">N/A</option>
		<?php 
		$covers = "SELECT * FROM Images ORDER BY name ASC";
		$cover_query = $mysqli->query($covers);
		while ($row = $cover_query->fetch_assoc()) {
			echo "<option value='$row[filePath]'>$row[name]</option>";
		}
		?>
	</select><br>
	<label for='newalbumdes'>New Album Description: </label><br>
	<textarea name='newalbumdes' id='newalbumdes' rows='4' cols='50'></textarea><br>

	<input type="submit" name= "submit-edit-fields" value="Submit">
</form>

<!-- FORM FOR CHANGING IMAGE INFORMATION --> 
<form action="add.php?action=edit" method="post"> 
	<h3> Update Image: </h3>
	<p> If you leave the field blank, it remains the same. </p>
	<label for="imageedit">Image to Update*: </label>
	<select name='imageedit' id='imageedit'>
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
	<label for="newimagename">New Image Name: </label>
	<input type="text" id='newimagename' name="newimagename" placeholder="New Image Name"><br>
	<label for="newimagecreds">New Credits: </label>
	<input type="text" id='newimagecreds' name="newimagecreds" placeholder="Reworked Credits"><br>
	<label for='newimagecap'>New Image Caption: </label><br>
	<textarea name='newimagecap' id='newimagecap' rows='4' cols='50'></textarea><br>

	<input type="submit" name= "submit-edit-images" value="Submit">
</form>

<!-- FORM FOR CHANGING IMAGE/ALBUM RELATIONSHIPS -->
<form action="add.php?action=edit" method="post">
	<h3>Update Images in Albums: </h3>
	<label for="albumadd">Album to Update*: </label>
	<select name='albumadd' id='albumadd'>
		<?php 
		$albums = "SELECT * FROM Albums ORDER BY albumName ASC";
		$result = $mysqli->query($albums);
		while ($row = $result->fetch_assoc()) {
			echo "<option value='$row[albumID]'>$row[albumName]</option>";
		}
		?>
	</select><br>
	<label for="imageadd">Image to Update*: </label>
	<select name='imageadd' id='imageadd'>
		<?php 
		$images = "SELECT * FROM Images ORDER BY name ASC";
		$result = $mysqli->query($images);
		while ($row = $result->fetch_assoc()) {
			echo "<option value='$row[imageID]'>$row[name]</option>";
		}
		?>
	</select><br>
	<input type="submit" name= "submit-add-to-album" value="Add">
	<input type="submit" name= "submit-remove-from-album" value="Remove">
</form>
