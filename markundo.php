<?php

include 'dbconn.php';
$conn = OpenCon();
$r = $_POST["r"];
$m = $_POST["m"];
$s = $_POST["s"];
$t = $_POST["t"];

$note = StampUndo($conn,$r,$m,$s,$t);
$flag = StampFlag($conn,$r,$m,$s);
echo $flag;
CloseCon($conn);

?>