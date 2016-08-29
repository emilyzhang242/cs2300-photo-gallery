<!DOCTYPE html>

<head>
	<title>Search</title>
	<meta charset="UTF-8">
	<link href='css/style.css' rel='stylesheet' type='text/css'>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<link href='https://fonts.googleapis.com/css?family=Sorts+Mill+Goudy' rel='stylesheet' type='text/css'>
</head>
<body>
	<div class="container">
		<?php include 'header.php'; ?>
	</div>
	<div class="header">
		<div class="container">
			<h1>Search Images & Albums</h1>
		</div>
	</div>

	<div class="container">
		<div id="content">
			<div id="text">
				<form action="search.php" method="post"> 
					<h3> Search: </h3>
					<?php
					if (isset($_POST['submit-search']) && !isset($_POST['search'])) {
						echo "<p class='warning'>Please enter some search criteria! </p>";
					}
					?>
					<label for="search-pic">Enter any keywords: </label>
					<input type="text" id="search-pic" name="search" placeholder="not case-sensitive"><br>
					<input type="submit" name= "submit-search" value="Submit">
				</form>		
			</div>
			<div class='container'>
				<?php

				$table = False;

				if (isset($_POST['submit-search']) && isset($_POST['search'])) {

					$search = htmlentities($_POST['search']);

					include_once 'config.php';
					$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					if ($mysqli->connect_error) {
						die ("CONNECTION FAILED: ".$mysqli->connect_error);
					}

					$sql = "SELECT * FROM Images i LEFT JOIN ImagesToAlbums q ON i.imageID=q.imageID LEFT JOIN Albums a ON q.albumID=a.albumID
					WHERE i.name LIKE '%$search%' OR i.caption LIKE '%$search%' OR i.credit LIKE '%$search%' OR 
					a.albumName LIKE '%$search%'
					GROUP BY i.imageID
					ORDER BY i.name";

					$result = $mysqli->query($sql);

					if (!$result) {
						echo "<p class='table-text'> Something is wrong with your search. </p>";
					}else {
						echo "<table><tr>";
						$max_cols = 4;
						$counter = 1;
						while ($row = $result->fetch_assoc()) {

							if (!is_null($row)) {
								$table = True;
							}

							$search_albums = $mysqli->query("SELECT * FROM Images i LEFT JOIN ImagesToAlbums q ON i.imageID=q.imageID LEFT JOIN Albums a ON q.albumID=a.albumID WHERE 
								i.imageID='$row[imageID]'");

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
							echo "<td>";
							echo "<a href='index.php?photo_id=$row[imageID]'><div class='table-div' style='background-image: url($row[filePath])'></div></a>";
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
					if ($table == False) {
						echo "<p class='table-text'>No results matched your search criteria.</p>";
					}
				}
				?>
			</div>
			<div class="container"></div>
		</div>
	</div>
	<p class="credits">Wallpaper by SubtlePatterns.com</p>

</body>
</html>
