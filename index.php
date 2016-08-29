<!DOCTYPE html>

<head>
	<title>Emily's Photos: Home</title>
	<meta charset="UTF-8">
	<link href='css/style.css' rel='stylesheet' type='text/css'>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<link href='https://fonts.googleapis.com/css?family=Sorts+Mill+Goudy' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
	
</head>
<body>

	<div class="container">
		<?php include 'header.php'; ?>
	</div>
	<div class="header">
		<div class="container">
			<a href='index.php'><h1>Flora & Fauna</h1></a>
		</div>
	</div>
	<div class="container">
		<div class="icon">
			<a href='index.php?albums=true'><i id='icon-album' class="fa fa-camera-retro"><br><p id='album' class="icons">Albums</p></i></a>
			<a href='index.php?photos=true'><i id='icon-photo' class="fa fa-photo"><br><p id='photos' class="icons">Photos</p></i></a>

		</div>
	</div>
	<div class="container">
		<div id="content">
			<div id="text">
				<?php
				if (isset($_GET['album_id']) && is_numeric($_GET['album_id'])){

					echo "<div class='container'><p id='logout'><a href='?albums=true'>Back</a></p></div>";

					require_once 'config.php';

					$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					if ($mysqli->connect_error) {
						die ("CONNECTION FAILED: ".$mysqli->connect_error);
					}
					$value = $_GET['album_id'];
					$result = $mysqli->query("SELECT albumName FROM Albums WHERE albumID=$value");
					while($row = $result->fetch_assoc()) {
						echo "<div class='container'><p class='table-title'>$row[albumName]</p></div>";
					}
				}else if (isset($_GET['photos'])) {
					echo "<div class='container'><p id='logout'><a href='?photos_null=true'>See photos not in any album</a></p></div>";
					echo "<h3> All Images </h3>";
				}else if (isset($_GET['photos_null'])) {
					echo "<div class='container'><p id='logout'><a href='?photos=true'>Back</a></p></div>";
					echo "<h3> Unsorted Photos </h3>";
				}else if (isset($_GET['photo_id'])) {
					echo "<div class='container'><p id='logout'><a href='?photos=true'>Back</a></p></div>";
				}else{
					echo "<h3> All Albums </h3>";
				}
				?>
			</div>
			<div class='container'>
				<?php 
				require_once 'config.php';
				$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

				if ($mysqli->connect_error) {
					die ("CONNECTION FAILED: ".$mysqli->connect_error);
				}
				$max_cols = 4;
				echo "<table><tr>";
				//get specific albums
				if (isset($_GET['album_id']) && is_numeric($_GET['album_id'])) {
					$value = $_GET['album_id'];
					$result = $mysqli->query("SELECT * FROM Images i
						WHERE i.imageID IN (SELECT a.imageID FROM ImagesToAlbums a
						WHERE a.albumID = $value)");
					$counter = 1;
					while($row = $result->fetch_assoc()) {
						echo "<td>";
						echo "<a href='?photo_id=$row[imageID]'><div class='table-div' style='background-image: url($row[filePath])'></div></a>";
						echo "</td>";
						if ($counter % $max_cols == 0) {
							$counter = 0;
							echo "</tr><tr>";
						}
						$counter = $counter + 1;
						
					}
					while ($counter <= $max_cols) {
						echo "<td>&nbsp;</td>";
						$counter++;
					}
					echo "</tr></table>";
				//match lists of images
				}else if (isset($_GET['photos']) || isset($_GET['photos_null'])) {
					$result = "";
					//show all images
					if (isset($_GET['photos']) && $_GET['photos'] == "true") {
						$result = $mysqli->query("SELECT * FROM Images ORDER BY name ASC");
					// match images not in any photo set 
					}else if (isset($_GET['photos_null']) && $_GET['photos_null'] == "true") {
						$result = $mysqli->query("SELECT * FROM Images i WHERE i.imageID NOT IN (SELECT q.imageID FROM ImagesToAlbums q)
							ORDER BY i.name ASC");
					}
					$counter = 1;
					while($row = $result->fetch_assoc()) {
						echo "<td>";
						echo "<a href='?photo_id=$row[imageID]'><div class='table-div' style='background-image: url($row[filePath])'></div></a>";
						echo "</td>";
						if ($counter % $max_cols == 0) {
							$counter = 0;
							echo "</tr><tr>";
						}
						$counter = $counter + 1;
					}
					while ($counter <= $max_cols) {
						echo "<td>&nbsp;</td>";
						$counter++;
					}
					echo "</tr></table>";
				//match individual photo
				}else if (isset($_GET['photo_id']) && is_int(intval($_GET['photo_id']))) {
					$id = htmlentities($_GET['photo_id']);
					$result = $mysqli->query("SELECT * FROM Images WHERE imageID = $id");
					$row = $result->fetch_assoc();

					if (!is_null($row)) {
						$search_albums = $mysqli->query("SELECT * FROM Images i LEFT JOIN ImagesToAlbums q ON i.imageID=q.imageID LEFT JOIN Albums a ON q.albumID=a.albumID WHERE i.imageID=$id");

						$albums = "";
						while ($new_row = $search_albums->fetch_assoc()) {
							if ($albums != "") {
								$albums = $albums." & ".$new_row['albumName'];
							}else{
								$albums = $new_row['albumName'];
							}
						}
						if ($albums == "") {
							$albums = "None";
						}

						echo "<tr>";
						echo "<td>";
						echo "<div class='ind-photo' style='background-image:url($row[filePath])'><br></div>";
						echo "<p class='table-title'>$row[name]</p>";
						echo "<p class='table-text'>$row[caption]</p>";
						echo "<p class='table-text'>Albums: $albums</p>";
						echo "<p class='table-text'> Credit goes to: $row[credit]</p><br>";
						echo "</td>";
						echo "</tr>";
					}else{
						echo "<p class='table-text'> This image doesn't exist! </p>";
					}
				//show albums generally
				}else{
					$result = $mysqli->query("SELECT * FROM Albums ORDER BY albumName ASC");
					$counter = 1;
					while($row = $result->fetch_assoc()) {
						echo "<td>";
						echo "<a href='?album_id=$row[albumID]'><div class='table-div' style='background-image: url($row[cover])'></div></a>";;
						echo "<p class='table-text'>$row[albumName]</p>";  
						echo "</td>";
						if ($counter % $max_cols == 0) {
							$counter = 0;
							echo "</tr><tr>";
						}
						$counter = $counter + 1;
					}
					while ($counter <= $max_cols) {
						echo "<td>&nbsp;</td>";
						$counter++;
					}
					echo "</tr></table>";
				}
				?>
			</div>
			<div class='container'></div>
		</div>
	</div>
	<div class="container"></div>
	<p class="credits">Wallpaper by SubtlePatterns.com</p>

</body>
</html>
