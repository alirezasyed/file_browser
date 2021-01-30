<?php
include "header.php";

$home = "home";
if (!is_dir($home)) {
  mkdir("home");
}

// chdir(getcwd() . DIRECTORY_SEPARATOR . $home);

if (!isset($_POST["cwd"])) {
  $cwd = getcwd() . DIRECTORY_SEPARATOR . $home;
}
else {
  $cwd = $_POST["cwd"];
}


if (!isset($_POST["sort"])) {
  $sort_by = "name";
}
else {
  $sort_by = $_POST["sort"];
}


if (!isset($_POST["sort_order"])) {
  $sort_order = "up";
}
else {
  if ($_POST["sort_order"] === "up") {
    $sort_order = "down";
  }
  else {
    $sort_order = "up";
  }
}

if (!isset($_POST["show_hidden"])) {
  $show_hidden = "show";
}
else {
  if ($_POST["show_hidden"] === "show") {
    $show_hidden = "hide";
  }
  else {
    $show_hidden = "show";
  }
}

chdir($cwd);

$all_contents = scandir($cwd);
// print_r($all_contents);

$contents = [];
$contents_size = [];
$contents_date = [];
$contents_type = [];


foreach ($all_contents as $item) {
  if ($item !== "." && $item !== "..") {
    // echo $item ."<br>"; // ou : echo "$item<br>";
    $contents[$item] = $item;
    $contents_date[$item] = filemtime($cwd . DIRECTORY_SEPARATOR . $item);
    if (is_dir($cwd . DIRECTORY_SEPARATOR . $item)) {
      $contents_size[$item] = "";
      $contents_type[$item] = "Folder";
    }
    else {
      $contents_size[$item] = filesize($cwd . DIRECTORY_SEPARATOR . $item);
      if (strpos(substr($item,1), ".")) {
        $type = explode(".", $item);
        $contents_type[$item] = end($type);
      }
      else {
        $contents_type[$item] = "undefined";
      }
    }
    }
  }

if ($sort_by === "date") {
  $sorted_contents = $contents_date;
  asort($sorted_contents);
}
elseif ($sort_by === "size") {
  $sorted_contents = $contents_size;
  asort($sorted_contents);
}
elseif ($sort_by === "type") {
  $sorted_contents = $contents_type;
  natcasesort($sorted_contents);
}
else {
  $sorted_contents = $contents;
  natcasesort($sorted_contents);
}

if ($sort_order === "down") {
  $sorted_contents = array_reverse($sorted_contents);
}



$breadcrumb = explode(DIRECTORY_SEPARATOR, $cwd);
$cwd_road = "";

$is_home = false; /* la variable indique si on est arrivé à "home" ou pas*/

echo "<form id='changecwd' method='POST'></form>";
echo "<form id='sort' method='POST'>";
  echo "<input type='hidden' name='cwd' value='$cwd'>";
  echo "<input type='hidden' name='sort_order' value='$sort_order'>";
echo "</form>";

echo "<form id='sort' method='POST'>";
  echo "<input type='hidden' name='cwd' value='$cwd'>";
  echo "<input type='hidden' name='sort_order' value='$sort_order'>";
  echo "<input type='hidden' name='sort' value='$sort_by'>";
echo "</form>";

echo "<form id='show_hidden' method='POST'>";
  echo "<input type='hidden' name='cwd' value='$cwd'>";
  echo "<input type='hidden' name='sort_order' value='$sort_order'>";
echo "</form>";

echo "<div class='container row'>";
foreach ($breadcrumb as $name) {
  $cwd_road .= $name . DIRECTORY_SEPARATOR; // ou $cwd_road = $cwd_road.$name . DIRECTORY_SEPARATOR; car .= est un opérateur concaténant
  if ($name === "$home") {
    $is_home = true; /* Quand on arrive à "home" alors "true" ... */
  }
  if ($is_home) { /*...et si on est passé après "home" on affiche les boutons*/
    echo "<div class='d-flex'>";
        echo "<button type='submit' form='changecwd' name='cwd' value='" . substr($cwd_road, 0, -1) . "'>";
        echo $name;
        echo "</button>";
    echo "</div>";
  }
}
echo "</div>";
echo "<div class='container'>";
  echo "<div class='breadcrumb'>";
    echo "<button type='submit' form='show_hidden' name='show_hidden' value='$show_hidden'>";
    if ($show_hidden === 'show') {
      echo "Show";
    }
    else {
      echo "Hide";
    }
    echo "</button>";
  echo "</div>";
  echo "<div class='breadcrumb'>";
    echo "<div class='w-25'>";
      echo "<button type='submit' form='sort' name='sort' value='name'>";
      echo "Name";
      echo "</button>";
    echo "</div>";
    echo "<div class='w-25'>";
      echo "<button type='submit' form='sort' name='sort' value='date'>";
      echo "Date";
      echo "</button>";
    echo "</div>";
    echo "<div class='w-25'>";
      echo "<button type='submit' form='sort' name='sort' value='size'>";
      echo "Size";
      echo "</button>";
    echo "</div>";
    echo "<div class='w-25'>";
      echo "<button type='submit' form='sort' name='sort' value='type'>";
      echo "Type";
      echo "</button>";
    echo "</div>";
  echo "</div>";
foreach ($sorted_contents as $name => $value) {
  if (!($name[0] === "." && $show_hidden === 'show')) {
    echo "<div class='breadcrumb'>";
      echo "<div class='w-25'>";
          echo "<button type='submit' form='changecwd' name='cwd' value='" . $cwd . DIRECTORY_SEPARATOR . $name . "'>";
          echo $name;
          echo "</button>";
        echo "</div>";
      echo "<div class='w-25'>";
          echo date("d-m-Y à H:i:s", $contents_date[$name]);
        echo "</div>";
      echo "<div class='w-25'>";
          echo $contents_size[$name];
        echo "</div>";
      echo "<div class='w-25'>";
          echo $contents_type[$name];
        echo "</div>";
    echo "</div>";
  }
}
echo "</div>";

include "footer.php";
