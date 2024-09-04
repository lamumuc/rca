<?php

include 'dbconn.php';
$conn = OpenCon();
$sOld = $_POST["sOld"];
$sNew = $_POST["sNew"];

// Change s in entry
$query = "UPDATE entry SET s='$sNew' WHERE s='$sOld'";
$content = ($conn->query($query));
$num_entry = ($content !== false) ? $conn->affected_rows : 0;

// Change s in stamp
$query = "UPDATE stamp SET s='$sNew' WHERE s='$sOld'";
$content = ($conn->query($query));
$num_stamp = ($content !== false) ? $conn->affected_rows : 0;

// Change s in result
$query = "UPDATE result SET s='$sNew' WHERE s='$sOld'";
$content = ($conn->query($query));
$num_result = ($content !== false) ? $conn->affected_rows : 0;

echo 'Racesail changed: entry ' . $num_entry . ' stamp ' . $num_stamp . ' result ' . $num_result;
CloseCon($conn);

?>