<?php

include 'dbconn.php';
$conn = OpenCon();
$r = isset($_POST["r"]) ? $_POST["r"] : 'NULL';
$sList = isset($_POST["sList"]) ? $_POST['sList'] : 'NULL';
$code = isset($_POST["code"]) ? $_POST["code"] : 'NULL';


$query = "SELECT s FROM result WHERE r = '$r' AND code = '$code'";
$content = mysqli_query($conn, $query);

if ($content) {
    $sRead = array();
    while ($row = mysqli_fetch_assoc($content)) {
        $sRead[] = $row['s'];
    }
}

if ($sList == 'null') { // get list of s with r,code
    echo implode("\n", $sRead);
} else { // send code to result with r,s
    $sWrite = explode("\n", $sList);
    $sWrite = array_map('trim', $sWrite);
    $sListDNF = array_diff($sRead, $sWrite);
    $sListNEW = array_diff($sWrite, $sRead);

    // Handle the s values not found in $sList
    foreach ($sListDNF as $s) {
        $s = trim($s);  // Trim leading/trailing whitespace and sanitize the input if necessary
        if (empty($s)) { continue; } // Skip empty lines

        $query = "UPDATE result SET code = 'DNF' WHERE r = '$r' AND s = '$s'";
        $content = mysqli_query($conn, $query);

        if ($content) {
            $procedureQuery = "CALL updateRank('$r', '$s')";
            $procedureContent = mysqli_query($conn, $procedureQuery);
        }
    }

    // Handle the new s values
    foreach ($sListNEW as $s) {
        $s = trim($s);  // Trim leading/trailing whitespace and sanitize the input if necessary
        if (empty($s)) { continue; } // Skip empty lines

        $query = "UPDATE result SET code = '$code' WHERE r = '$r' AND s = '$s'";
        $content = mysqli_query($conn, $query);

        if ($content) {
            $procedureQuery = "CALL updateRank('$r', '$s')";
            $procedureContent = mysqli_query($conn, $procedureQuery);
        }
    }
}

CloseCon($conn);

?>