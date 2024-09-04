<?php

include 'dbconn.php';
$conn = OpenCon();
$r = $_POST["r"];
$m = $_POST["m"];
$s = $_POST["s"];
$t = $_POST["t"];
$o = $_POST["o"];

if ($o == 1) { // offset here
    $offset = StampTime($conn,50,50,"bOffset");
    if (!empty($offset)) {
        $offset = end($offset);
        if ($offset > 90000000) { 
            $t = $t -($offset - 90000000); 
        } else {
            $t = $t + $offset;
        }
    }
}

$note = StampSend($conn,$r,$m,$s,$t);
$flag = StampFlag($conn,$r,$m,$s);
echo $flag;
CloseCon($conn);

?>