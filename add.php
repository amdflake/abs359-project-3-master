<?php include("includes/init.php");

$sql3 = $db->prepare("SELECT tag FROM tags");
$sql3->execute();
$tagarray = $sql3->fetchAll(PDO::FETCH_COLUMN);
$success = FALSE;
$valid = TRUE;
$typecheck = TRUE;

if (isset($_POST["upload"])) {

  $target_file = "uploads/images/" . basename($_FILES["file_upload"]["name"]);
  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  if ($imageFileType == 'jpg' || $imageFileType == 'jpeg' || $imageFileType == 'png'){

  $upload = $_FILES['file_upload'];
  $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
  $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_STRING);
  $ext = $_POST['type'];
  $chosen_tags = $_POST['tags'];

  if($upload['error'] == 'UPLOAD_ERROR_OK'){

    $sql = "INSERT INTO images (file_ext, author, description)
    VALUES (:ext, :author, :description)";
    $params = array(
      ':ext' => $ext,
      ':author' => $author,
      ':description' => $description
    );
    exec_sql_query($db, $sql, $params);
  }

  $sql2 = $db->prepare("SELECT id FROM images");
  $sql2->execute();
  $array = $sql2->fetchAll(PDO::FETCH_COLUMN);
  $last_id = max($array);

  $new_path = "uploads/images/$last_id.$ext";
  move_uploaded_file($_FILES["file_upload"]["tmp_name"], $new_path);

  $newtag = $_POST['newtag'];

  if(empty(trim($newtag)) == FALSE and in_array($newtag, $tagarray) == FALSE){

    $tagadd = filter_input(INPUT_POST, 'newtag', FILTER_SANITIZE_STRING);
    $sql5 = "INSERT INTO tags (tag) VALUES (:tagadd)";
    $params5 = array(
      ':tagadd' => $tagadd
    );
    exec_sql_query($db, $sql5, $params5);

    $sql6 = $db->prepare("SELECT id FROM tags");
    $sql6->execute();
    $tagarray = $sql6->fetchAll(PDO::FETCH_COLUMN);
    $last_tagid = max($tagarray);

    $sql7 = "INSERT INTO image_tags (image_id, tag_id) VALUES (:last_id, :last_tagid);";
    $params7 = array(
      ':last_id' => $last_id,
      ':last_tagid' => $last_tagid
    );
    exec_sql_query($db, $sql7, $params7);
  }
  elseif (in_array($newtag, $tagarray)){
    array_push($chosen_tags, $newtag);
    $valid = FALSE;
  }


  foreach ($chosen_tags as $tag){
    $sql3 = "SELECT id FROM tags WHERE tag = :tag;";
    $params = array(
      ':tag' => $tag
    );
    $tmparray = exec_sql_query($db, $sql3, $params)->fetchAll(PDO::FETCH_ASSOC);

    $tag_id = $tmparray[0]['id'];

    $sql4 = "INSERT INTO image_tags (image_id, tag_id) VALUES (:last_id, :tag_id);";
    $params2 = array(
      ':last_id' => $last_id,
      ':tag_id' => $tag_id
    );
    exec_sql_query($db, $sql4, $params2);

  }

  $success = TRUE;

}else{
  $typecheck = FALSE;
}
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles/site.css" media="all" />

  <title>TODO</title>
</head>

<body>
<?php include("includes/head.php");

if($success == FALSE && $typecheck){
?>

<h1>Add an entry</h1>
<form id="uploadFile" method="post" enctype="multipart/form-data" action="add.php">

  <input type="hidden" name="MAX_FILE_SIZE" value="10000000">

  <label for=file_upload>Upload Image:</p>
  <input type=file name=file_upload><br><br>

  <label for=type>File type:</label>
  <select name=type>
    <option value=jpg>jpg</option>
    <option value=png>png</option>
    <option value=jpeg>jpeg</option>
  </select>
  <br><br>

  <label for=author>Author Name:</label>
  <input name=author type=text></input>
  <br><br>
  <label for=description>Image Description/Caption</label><br>
  <textarea name=description></textarea><br><br>

  <label for=tags>Check all tags that apply</label><br>

  <?php
  foreach ($tagarray as $tag){
    echo  "<input type=checkbox name=tags[] value=" . $tag . ">" . $tag . "</input><br>";
    }
  ?>

  <label for=newtag>Don't see a tag you want? Add one here and we'll add it to your image: </label>
  <input type=text name=newtag><br>

<input type=submit name=upload value="Add Entry"></input>
</form>
<br>
<p><a href=gallery.php>Return to Gallery</a></p>

  <?php } elseif($typecheck == FALSE){?>
    <p>The file you uploaded is not a valid type.</p>
  <p><a href=gallery.php>Return to Gallery</a></p>

  <?php }else{ ?>
  <p>Your upload was a success! Click the link below to check out your new post in the gallery</p>
  <p><a href=gallery.php>Return to Gallery</a></p>
  <?php }?>
</body>

</html>
