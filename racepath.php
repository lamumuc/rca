<?php

include 'dbconn.php';
$conn = OpenCon();
$r = isset($_POST["r"]) ? $_POST["r"] : 'NULL';
$mode = isset($_POST["mode"]) ? $_POST["mode"] : 'NULL';
$gp = isset($_POST["gp"]) ? $_POST["gp"] : 'NULL';
$path = isset($_POST["path"]) ? $_POST["path"] : 'NULL';

// delete route / edit path / change mode / delete race / add new race
if ($mode == "DEL") {
    // if mode == "DEL", delete the race and all stamp and result
    $query = "DELETE FROM route WHERE r='$r'";
    $content = ($conn->query($query));
    $query = "DELETE FROM stamp WHERE r=$r OR r=$r+50";
    $content = ($conn->query($query));
    $query = "DELETE FROM result WHERE r=$r OR r=$r+50";
    $content = ($conn->query($query));
    echo 'Race ' . $r . ' deleted ';
} else {
//if r,mode exist, edit path
    $query = "SELECT * FROM route WHERE r='$r' AND mode='$mode'";
    $content = ($conn->query($query));

    if ($content->num_rows > 0) {
        if ($mode == "FD") {
            $query = "UPDATE route SET path='$path' WHERE r='$r' AND mode='$mode'";
        } else {
            $query = "UPDATE route SET path='$path' WHERE r='$r' AND mode='$mode' AND gp='$gp'";
        }
        $content = ($conn->query($query));
        echo 'Race path updated to ' . $r . ' ' . $mode . ' ' . $gp . ' ' . $path;
    } else {

//if r exist, mode different, delete mode and add new mode
        $query = "SELECT * FROM route WHERE r='$r' AND mode!='$mode'";
        $content = ($conn->query($query));
        
        if ($content->num_rows > 0) {
            $query = "DELETE FROM route WHERE r='$r' AND mode!='$mode'";
            $content = ($conn->query($query));  // Delete the race but KEEP stamp and result
        } 

//if r not exist, add route[r,mode,copy all gp&path from r=0], stamp[r,m=92,s=cutoff,t=50], stamp[r,m=93,s=limits,t=90]
        $query = "INSERT INTO route (r, mode, gp, path) SELECT '$r' AS r, '$mode' AS mode, gp, path FROM route WHERE r=0;";
        $content = ($conn->query($query));
    
        if ($mode == "FD") {
            $query = "UPDATE route SET path='123456F' WHERE r='$r'";     // same path for all gp
            $content = ($conn->query($query));
            $query = "UPDATE stamp SET t=5400000 WHERE r='$r' AND s='limits'"; // time limits
            $content = ($conn->query($query));
        } else {
            $query = "UPDATE stamp SET t=3000000 WHERE r='$r' AND s='limits'"; // time limits
            $content = ($conn->query($query));
        }
        echo 'Race ' . $r . ' ' . $mode . ' added ';
    }
}
CloseCon($conn);

?>