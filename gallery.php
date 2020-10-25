<?php include("includes/init.php");

$taglist = $db->prepare("SELECT tag FROM tags");
  $taglist->execute();
  $array = $taglist->fetchAll(PDO::FETCH_COLUMN);

http_build_query($array);

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(isset($_POST['deletebutton'])){
  $deleteid = $_POST["hiddendelete"];
  $sqldelete = "DELETE FROM images WHERE id = :deleteid;";
  $sqldelete2 = "DELETE FROM image_tags WHERE image_id = :deleteid;";
  $deleteparams = array(
    ':deleteid' => $deleteid
  );
  unlink("uploads/images/" . $deleteid . ".jpg");
  unlink("uploads/images/" . $deleteid . ".jpeg");
  unlink("uploads/images/" . $deleteid . ".png");

  exec_sql_query($db, $sqldelete, $deleteparams);
  exec_sql_query($db, $sqldelete2, $deleteparams);
}
elseif(isset($_POST['tagform'])){
  $tagdelete = $_POST['hiddentag'];
  $taggetid = 'SELECT id FROM tags WHERE tag = :tagdelete';
  $taggetidparams = array(
    ':tagdelete' => $tagdelete
  );
  $tagdeletearray = exec_sql_query($db, $taggetid, $taggetidparams)->fetchAll(PDO::FETCH_ASSOC);
  $tagdeleteid = $tagdeletearray[0]['id'];
  $sqltagdelete = "DELETE FROM image_tags WHERE tag_id = :tagdeleteid AND image_id = :hiddenimageid";
  $hiddenimageid = $_POST['hiddenimageid'];
  $tagparams = array(
    ':tagdeleteid' => $tagdeleteid,
    ':hiddenimageid' => $hiddenimageid
  );
  exec_sql_query($db, $sqltagdelete, $tagparams);
}
elseif(isset($_POST['texttagadd'])){
  $validtag = TRUE;
  $newtag = filter_input(INPUT_POST, 'texttagadd', FILTER_SANITIZE_STRING);
  $tagaddimageid = $_POST['tagaddimageid'];

  $sql6 = "SELECT tags.tag FROM tags INNER JOIN image_tags ON tags.id = image_tags.tag_id INNER JOIN images ON image_tags.image_id = images.id WHERE images.id = :tagaddimageid;";
  $params6 = array(
    'tagaddimageid' => $tagaddimageid
  );
  $tagarraycheck = exec_sql_query($db, $sql6, $params6)->fetchAll(PDO::FETCH_ASSOC);
  foreach ($tagarraycheck as $tag){
   if($newtag == $tag['tag']){
     $validtag = FALSE;
   }
  }
  if($validtag){

  if(in_array($newtag, $array) == FALSE){
    $sqladdtag = 'INSERT INTO tags (tag) VALUES (:newtag)';
    $addtagparams = array(
      ':newtag' => $newtag
    );
    exec_sql_query($db, $sqladdtag, $addtagparams);

  }

  $newtagidquery = 'SELECT id FROM tags WHERE tag = :newtag';
  $newtagidparams = array(
    'newtag' => $newtag
  );
  $tagidarray = exec_sql_query($db, $newtagidquery, $newtagidparams)->fetchAll(PDO::FETCH_ASSOC);
  $newtagid = $tagidarray[0]['id'];

  $sqlnewtag = 'INSERT INTO image_tags (image_id, tag_id) VALUES (:tagaddimageid, :newtagid);';
  $newtagparams = array(
    ':tagaddimageid' => $tagaddimageid,
    ':newtagid' => $newtagid
  );
  exec_sql_query($db, $sqlnewtag, $newtagparams);
}
}
}

function tagselect ($array) {
  foreach ($array as $tag){
    echo "<option value=" . $tag . ">" . $tag . "</option>"; }
}

if(isset($_GET['tagsearch']) and $_GET['tag'] != "all") {
  $sql = "SELECT * FROM images INNER JOIN image_tags ON images.id = image_tags.image_id INNER JOIN tags ON image_tags.tag_id = tags.id WHERE tags.tag = :tag;";
  $params = array(
    ':tag' => $_GET['tag']
  );
  $images = exec_sql_query($db, $sql, $params)->fetchAll(PDO::FETCH_ASSOC);
  http_build_query($_GET['tagsearch']);
}
else{
  $images = exec_sql_query($db, "SELECT * FROM images")->fetchAll(PDO::FETCH_ASSOC);
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

<?php include("includes/head.php")?>

<h1 class=indicate>Currently Viewing: <?php
if(isset($_GET['tagsearch'])) {
  if ($_GET['tag'] == "all"){
    echo "All Images";
  }
else{
  echo "Images with " . htmlspecialchars($_GET['tag']) . " tag";
}
}
else{
  echo "All Images";
}
  ?>
</h1> <br>
<p class=add>Add Image</p>
<a href=add.php><h1 class=entry>+</h1></a>

<h3>Want to search by category?</h3>
<p>Choose a tag to search: </p>
<form method=get>
<select id="tag1" name="tag">
  <option value=all>All images</option>
  <?php
    tagselect($array);
    ?>
</select>

<button name="tagsearch" type="submit">Apply</button>

</form><br><br>
<?php
    if(isset($_GET['tagsearch']) and $_GET['tag'] != "all") {

      foreach ($images as $image) {

        $imageid = $image["image_id"];
        $desc = $image["description"];
        $auth = $image["author"];

        if(empty(trim($desc))){
          $desc = "<em>No Description</em>";
        }
        if(empty(trim($auth))){
          $auth = "<em>Anonymous</em>";
        }

        $sql3 = "SELECT tags.tag FROM tags INNER JOIN image_tags ON tags.id = image_tags.tag_id INNER JOIN images ON image_tags.image_id = images.id WHERE images.id = :imageid;";
        $params = array(
          'imageid' => $imageid
        );
        $tagarray = exec_sql_query($db, $sql3, $params)->fetchAll(PDO::FETCH_ASSOC);

        echo "<div class='images'> <br><img src=uploads/images/" . $imageid . "." . $image["file_ext"] . "><form method=post><input type=hidden name=hiddendelete value=" . $imageid . "><input type=submit class=delete align=right value=remove name=deletebutton></form>
        <p class=author><strong>Creator</strong>: " . $auth . "</p><p class=description><strong>Description</strong>: " . $desc . "</p> <br> <p class=tags>";

        foreach ($tagarray as $tag){
          echo '<form method=post><input type=hidden name=hiddentag value=' . $tag['tag'] . '><input type=hidden name=hiddenimageid value=' . $imageid . '>
          <input class=tagdelbutton name=tagform type=submit value=#' . $tag['tag'] . "></form>";
        }
        echo "<br><form method=post><input type=hidden name=tagaddimageid value=" . $imageid . "><input type=text name=texttagadd placeholder='Add a tag'><input type=submit name=tagaddbutton value='+'></form>";
        ?></p><br><br></div> <br><br> <?php
      }
    }

    else{
      foreach ($images as $image) {

        $imageid = $image["id"];
        $desc = $image["description"];
        $auth = $image["author"];

        if(empty(trim($desc))){
          $desc = "<em>No Description</em>";
        }
        if(empty(trim($auth))){
          $auth = "<em>Anonymous</em>";
        }

        $sql3 = "SELECT tags.tag FROM tags INNER JOIN image_tags ON tags.id = image_tags.tag_id INNER JOIN images ON image_tags.image_id = images.id WHERE images.id = :imageid;";
        $params = array(
          'imageid' => $imageid
        );
        $tagarray = exec_sql_query($db, $sql3, $params)->fetchAll(PDO::FETCH_ASSOC);

        echo "<div class='images'> <br><img src=uploads/images/" . $imageid . "." . $image["file_ext"] . "><form method=post><input type=hidden name=hiddendelete value=" . $imageid . "><input type=submit class=delete align=right value=remove name=deletebutton></form>
        <p class=author><strong>Creator</strong>: " . $auth . "</p><p class=description><strong>Description</strong>: " . $desc . "</p> <br> <p class=tags>";

        foreach ($tagarray as $tag){
          echo '<form method=post><input type=hidden name=hiddentag value=' . $tag['tag'] . '><input type=hidden name=hiddenimageid value=' . $imageid . '>
          <input class=tagdelbutton name=tagform type=submit value=#' . $tag['tag'] . "></form>";
        }
        echo "<br><form method=post><input type=hidden name=tagaddimageid value=" . $imageid . "><input type=text name=texttagadd placeholder='Add a tag'><input type=submit class=tagaddbutton name=tagaddbutton value='+'></form>";
        ?></p><br><br></div> <br><br> <?php
      }
    }
      ?>

</body>

</html>
