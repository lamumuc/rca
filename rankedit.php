<?php

include 'dbconn.php';
$conn = OpenCon();
$r = $_POST["r"];
$s = $_POST['s'];
$rank = $_POST["rank"];
$raPY = $_POST["raPY"];


$query = "UPDATE result SET `rank` = '$rank' , `rank_py` = '$raPY' WHERE r = '$r' AND s = '$s'";
$content = mysqli_query($conn, $query);

CloseCon($conn);

?>