<!DOCTYPE html>

<head>
	<title>Add & Edit</title>
	<meta charset="UTF-8">
	<link href='css/style.css' rel='stylesheet' type='text/css'>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<link href='https://fonts.googleapis.com/css?family=Sorts+Mill+Goudy' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
	<script src='js/add.js'></script>

	<?php

	session_start();
	if (isset($_GET['logout'])) {
		unset($_SESSION['loggedIn']);
		session_destroy();
	}

	?>
</head>
<body>
	<div class="container">
		<?php include 'header.php'; ?>
	</div>
	<div class="header">
		<div class="container">
			<h1>Add Albums & Images</h1>
		</div>
	</div>
	<div class="container">
		<div class="icon">
			<a href='add.php?action=add'><i id='icon-add' class="fa fa-plus"><br><p id='add' class="icons">Add</p></i></a>
			<a href='add.php?action=edit'><i id='icon-edit' class="fa fa-edit"><br><p id='edit' class="icons">Edit</p></i></a>
			<a href='add.php?action=delete'><i id='icon-remove' class="fa fa-times"><br><p id='delete' class="icons">Delete</p></i></a>


		</div>
	</div>

	<?php

	require_once 'config.php';

	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_error) {
		die ("CONNECTION FAILED: ".$mysqli->connect_error);
	}
	// this takes care of the add page
	$error = "";
	if (isset($_POST['submit-album']) && empty($_POST['album'])) {
		$error .= "Please enter an album name! <br>";
	}else if (isset($_POST['submit-album']) && !empty($_POST['album'])) {
		$value = htmlentities($_POST['album']);
		$desc = htmlentities($_POST['album-des']);

		require_once 'config.php';
		$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if ($mysqli->connect_error) {
			die ("CONNECTION FAILED: ".$mysqli->connect_error);
		}
		$mysqli->query("INSERT INTO Albums(albumName, dateCreated, dateModified, cover, description)
			VALUES('$value', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'images/default.jpeg','$desc')");
	}

	if (isset($_POST['submit-image'])) {
		if (empty($_POST['image'])) {
			$error .= "You didn't pick an image name! <br>";
		}
		if (empty($_POST['credit'])) {
			$error .= "You didn't give credit! <br>";
		}
		if (!isset($_FILES['file'])) {
			$error .= "You didn't select a file! <br>";
		}
		if (!empty($_POST['image']) && !empty($_POST['credit']) && isset($_FILES['file'])) {

			// add in functionality for multiple albums
			$name = htmlentities($_POST['image']);
			$credit = htmlentities($_POST['credit']);
			$caption = htmlentities($_POST['caption']);
			$photo = strtolower($name);
			$photo = str_replace(" ", "", $photo);
			$path = "images/".$photo.".jpg";
			$temp = $_FILES['file']['tmp_name'];

			//sql check to make sure names are different

			$check_names = "SELECT * FROM Images WHERE name='$name'";
			$check = $mysqli->query($check_names);

			if (is_null($check->fetch_assoc())) {
				if(move_uploaded_file($temp, $path)) {

					if (empty($_POST['album-img'])) {
						$mysqli->query("INSERT INTO Images(name, caption, credit, filePath)
							VALUES('$name', '$caption', '$credit', '$path')");
					}else{

						$albums = array();
						foreach ($_POST['album-img'] as $album) {
							$albums[] = $album;
						} 

					//adjust auto-increment
						$inc_num = "SELECT MAX(imageID) FROM Images";
						$result = $mysqli->query($inc_num);
						$row = $result->fetch_array();
						$num = $row[0]+1;
						$alter = "ALTER TABLE Images AUTO_INCREMENT=$num";
						$mysqli->query($alter);

						$mysqli->query("INSERT INTO Images(name, caption, credit, filePath)
							VALUES('$name', '$caption', '$credit', '$path')");
						$newid = $mysqli->insert_id;
						foreach ($albums as $album) {
							$mysqli->query("INSERT INTO ImagesToAlbums(albumID, imageID)
								VALUES('$album', '$newid')");
						}
					}
				}
			}else {
				$error .= "That picture name has already been chosen! Please pick another.";
			}
		}
	}

	// this takes care of the editing albums
	if (isset($_POST['submit-edit-fields'])) {

		$edited = False;

		$albumID = filter_input(INPUT_POST, 'albumedit', FILTER_SANITIZE_NUMBER_INT);

		if (isset($_POST['newalbumname']) && trim($_POST['newalbumname']) !== "") {
			$new_name = filter_input(INPUT_POST, 'newalbumname', FILTER_SANITIZE_STRING);
			$name = "UPDATE Albums SET albumName='$new_name' WHERE albumID=$albumID";
			$mysqli->query($name);
			$edited = True;
		}
		if (isset($_POST['albumcover']) && $_POST['albumcover'] !== "") {
			$new_album = filter_input(INPUT_POST, 'albumcover', FILTER_SANITIZE_STRING);
			$album = "UPDATE Albums SET cover='$new_album' WHERE albumID=$albumID";
			$mysqli->query($album);
			$edited = True;
		}
		if (isset($_POST['newalbumdes']) && $_POST['newalbumdes'] !== "") {
			$new_des = filter_input(INPUT_POST, 'newalbumdes', FILTER_SANITIZE_STRING);
			$desc = "UPDATE Albums SET description='$new_des' WHERE albumID=$albumID";
			$mysqli->query($desc);
			$edited = True;
		}
		//change modified date
		if ($edited == True) {
			$mysqli->query("UPDATE Albums SET dateModified=CURRENT_TIMESTAMP WHERE albumID=$albumID");
		}
	}

	//this takes care of editing images
	if (isset($_POST['submit-edit-images'])) {

		$imageID = filter_input(INPUT_POST, 'imageedit', FILTER_SANITIZE_NUMBER_INT);

		if (isset($_POST['newimagename']) && trim($_POST['newimagename']) !== "") {
			$new_name = filter_input(INPUT_POST, 'newimagename', FILTER_SANITIZE_STRING);
			$name = "UPDATE Images SET name='$new_name' WHERE imageID=$imageID";
			$mysqli->query($name);
		}
		if (isset($_POST['newimagecreds']) && $_POST['newimagecreds'] !== "") {
			$new_creds = filter_input(INPUT_POST, 'newimagecreds', FILTER_SANITIZE_STRING);
			$creds = "UPDATE Images SET credit='$new_creds' WHERE imageID=$imageID";
			$mysqli->query($creds);
		}
		if (isset($_POST['newimagecap']) && $_POST['newimagecap'] !== "") {
			$new_cap = filter_input(INPUT_POST, 'newimagecap', FILTER_SANITIZE_STRING);
			$creds = "UPDATE Images SET caption='$new_cap' WHERE imageID=$imageID";
			$mysqli->query($creds);
		}
	}

	// this takes of editing image/album relationships 
	if (isset($_POST['submit-add-to-album']) || isset($_POST['submit-remove-from-album'])) {
		$albumID = filter_input(INPUT_POST, 'albumadd', FILTER_SANITIZE_NUMBER_INT);
		$imageID = filter_input(INPUT_POST, 'imageadd', FILTER_SANITIZE_NUMBER_INT);
		$check =$mysqli->query("SELECT * FROM ImagesToAlbums WHERE imageID=$imageID AND albumID=$albumID");

		if (isset($_POST['submit-add-to-album'])) {
			if (is_null($check->fetch_assoc())) {
				$mysqli->query("INSERT INTO ImagesToAlbums VALUES($albumID, $imageID)");
			//already in album
			}else{
				$error .= "This image is already in the album!";
			}
		}else if (isset($_POST['submit-remove-from-album'])) {
			if (!is_null($check->fetch_assoc())) {
				$mysqli->query("DELETE FROM ImagesToAlbums WHERE imageID=$imageID AND albumID=$albumID");
		//is in album
			}else{
				$error .= "This image isn't in the album anyway!";
			}
		}
	}

	//this takes care of deleting albums
	if (isset($_POST['submit-delete-album'])) {
		$albumID = filter_input(INPUT_POST, 'albumdelete', FILTER_SANITIZE_NUMBER_INT);
		$result = $mysqli->query("SELECT * FROM Albums WHERE albumID=$albumID");
		//this makes sure they don't enter a random number
		if (!is_null($result->fetch_assoc())) {
			$mysqli->query("DELETE FROM Albums WHERE albumID=$albumID");
			$mysqli->query("DELETE FROM ImagesToAlbums WHERE albumID=$albumID");
		}else{
			$error .= "You have entered an album that does not exist!";
		}
	}

	//this takes care of deleting images
	if (isset($_POST['submit-delete-image'])) {
		$imageID = filter_input(INPUT_POST, 'imagedelete', FILTER_SANITIZE_NUMBER_INT);
		$result = $mysqli->query("SELECT * FROM Images WHERE imageID=$imageID");
		$name = "";
		if (!is_null($row = $result->fetch_assoc())) {
			$name = $row['name'];
			$mysqli->query("DELETE FROM Images WHERE imageID=$imageID");
			$mysqli->query("DELETE FROM ImagesToAlbums WHERE imageID=$imageID");
		}else{
			$error .= "You have entered an image that does not exist!";
		}
		//delete actual image from folder
		unlink('images/'.$name.'.jpg');
	}

	?>

	<div class="container">
		<div id="content">
			<div id="text">
				<?php

				// album errors
				if ($error !== "") {
					echo "<p class='warning'> $error </p>";
				}else if ($error == "" && isset($_POST['submit-album'])) {
					echo "<p> You've successfully added an album! </p>";
				}else if ($error == "" && isset($_POST['submit-image'])) {
					echo "<p> You've successfully added an image! </p>";
				}

				if ($error == "" && isset($_POST['submit-edit-fields'])) {
					echo "<p> You've successfully edited an album! </p>";
				}

				if ($error == "" && isset($_POST['submit-add-to-album'])) {
					echo "<p> You've successfully added an image into an album! </p>";
				}
				if ($error == "" && isset($_POST['submit-remove-from-album'])){
					echo "<p> You've successfully deleted an image from an album! </p>";
				}

				if ($error == "" && isset($_POST['submit-edit-images'])) {
					echo "<p> You've successfully edited an image! </p>";
				}

				if ($error == "" && isset($_POST['submit-delete-album'])) {
					echo "<p> You've successfully deleted an album! </p>";
				}
				if ($error == "" && isset($_POST['submit-delete-image'])) {
					echo "<p> You've successfully deleted an image! </p>";
				}

				//user log in functionality
				if (isset($_POST['submit-user'])) {
					if ($_POST['username']== "" || $_POST['password'] == "") {
						echo "<p class='warning'> You didn't enter a username or password! </p>";
					}else{
						$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
						$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

						//grab sql password to match
						include_once 'config.php';
						$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
						if ($mysqli->connect_error) {
							die ("CONNECTION FAILED: ".$mysqli->connect_error);
						}

						$sql = "SELECT * FROM Users WHERE userName='$username'";
						$result = $mysqli->query($sql);
						if ($result) {
							$row = $result->fetch_assoc();
							$sql_pass = $row['hashedPassword'];

							$valid_password = password_verify($password, $sql_pass);
							if ($valid_password) {
								$_SESSION['loggedIn'] = $username;

							}else{
								echo "<p class='warning'> Your username or password is incorrect! </p>";
							}
						}
					}
				}

				if (!isset($_SESSION['loggedIn'])) {
					include 'add_login.php';
				}else if (isset($_SESSION['loggedIn'])) {
					if (!isset($_GET['action']) || $_GET['action'] == "add") {
						include 'add_add.php';
					}else if ($_GET['action'] == "edit"){
						include 'add_edit.php';
					}else{
						include 'add_delete.php';
					}
				}
				?>


			</div>
		</div>
	</div>
	<div class="container"></div>
	<p class="credits">Wallpaper by SubtlePatterns.com</p>

</body>
</html>
