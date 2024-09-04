<?php
	include 'dbconn.php';
	$conn = OpenCon();		// echo "Connected";
    echo CurrR($conn);
    CloseCon($conn);
?>
