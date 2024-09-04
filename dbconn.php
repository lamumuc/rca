<?php
function OpenCon() {
    $conn = new mysqli("localhost","root","","rca") or die("Connect failed: %s\n". $conn -> error);
    return $conn;
}
 
function CloseCon($conn) {
    $conn -> close();
}
   
function PrepareTable($conn,$table,$condition) {   // db, table, orderby
    $query = "SELECT * FROM " . $table . $condition;
    $content = ($conn->query($query));
    $rows = [];

    if ($content->num_rows > 0) {
        $rows = $content->fetch_all(MYSQLI_ASSOC);
    }
    return $rows;
}

function CurrR($conn) {
    $query = "SELECT MAX(r) AS max_r FROM route";
    $content = ($conn->query($query));
    $row = $content->fetch_assoc();
    return (int) $row["max_r"];
}

function NumOfCateInGp ($conn,$gp) {
    $query = "SELECT COUNT(*) as num FROM grade WHERE gp='$gp'";
    $content = ($conn->query($query));
    $row = $content->fetch_assoc();
    return $row["num"];
}

function NumOfSInCate ($conn,$cate) {
    $query = "SELECT COUNT(s) as num FROM entry WHERE cate='$cate'";
    $content = ($conn->query($query));
    $row = $content->fetch_assoc();
    return $row["num"];
}

function NumOfSailFin ($conn,$r,$cate,$code) {
    $query = "SELECT COUNT(s) as num FROM result WHERE r='$r' AND s IN (SELECT s FROM entry WHERE cate='$cate') AND code='$code'";
    $content = ($conn->query($query));
    $row = $content->fetch_assoc();
    return $row["num"];
}

function TimeHHMMSSXXX($t) {
	// Unixtimestamp to "HH:MM:SS:XXX"
	return gmdate('H:i:s', floor($t/1000)). ':' . sprintf('%03d', $t%1000);
}

function TimeHHMMSS($t) {
	// Unixtimestamp to "HH:MM:SS"
	return gmdate('H:i:s', floor($t/1000));
}

function StampTime($conn,$r,$m,$s) {
    $query = "SELECT t FROM stamp WHERE r='$r' AND m='$m' AND s='$s'";
    $content = ($conn->query($query));
    if ($content->num_rows > 0) {
        $rows = $content->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $row) {
            $time[] = (int) $row['t'];
        }
        return $time;
    }
}

function StampSend($conn,$r,$m,$s,$t) {
    if ($m == 50) {
        $query = "UPDATE stamp SET t='$t' WHERE r='$r' AND m='$m' AND s='$s'";
        $content = ($conn->query($query));

        $query = "SELECT * FROM stamp WHERE r='$r' AND m='$m' AND s='$s'";
        $content = ($conn->query($query));

        if ($content->num_rows < 1) { // bOffset not exist
            $query = "INSERT INTO stamp (r,m,s,t) VALUES ('$r','$m','$s','$t')";
            $content = ($conn->query($query));
        }
        return 50;
    } else if ($m == 91) { // check if start time already set
        $query = "SELECT * FROM stamp WHERE r='$r' AND m='$m' AND s='$s'";
        $content = ($conn->query($query));

        if ($content->num_rows > 0) {
            return 91;
        }
    }

    $query = "INSERT INTO stamp (r,m,s,t) VALUES ('$r','$m','$s','$t')";
    $content = ($conn->query($query));

    if ($m == 99) { // if (r,99,"gp") not exist,  insert (r,99,"gp",t)
        $query = "SELECT code FROM result WHERE r='$r' AND s='$s'";
        $content = $conn->query($query);
        
        if ($content) {
            $row = $content->fetch_assoc();
            $code = $row['code'];
        
            if ($code == 'FIN') { // s FIN
                $query = "SELECT * FROM stamp WHERE r='$r' AND m='$m' AND s=(SELECT cate FROM entry WHERE s='$s')";
                $content = ($conn->query($query));
        
                if ($content->num_rows < 1) { // it is the first sail
                    $query = "INSERT INTO stamp (r,m,s,t) VALUES ('$r','$m',(SELECT cate FROM entry WHERE s='$s'),'$t')";
                    $content = ($conn->query($query));
                }
            }
        }
    } 
}

function StampUndo($conn,$r,$m,$s,$t) {
    if ($t == 9999) {
        // Find max t of r m s
        $query = "SELECT MAX(t) AS maxt FROM stamp WHERE r='$r' AND m='$m' AND s='$s'";
        $content = ($conn->query($query));

        if ($content->num_rows > 0) { // if stamp (r,m,s,t) found, delete it
            $rows = $content->fetch_all(MYSQLI_ASSOC);
            $t = $rows[0]['maxt'];
        }
    }

    $query = "DELETE FROM stamp WHERE  r='$r' AND m='$m' AND s='$s' AND t='$t'";
    $content = ($conn->query($query));

    if ($m == 99){  // if it is final mark, handle first sail, find the next sail
        $query = "DELETE FROM stamp WHERE  r='$r' AND m='99' AND s=(SELECT cate FROM entry WHERE s='$s') AND t='$t'";
        $content = ($conn->query($query));

        if ($conn->affected_rows > 0) { // stamp (r,99,"cate",t) deleted
            $query = "SELECT MIN(t) AS mint FROM stamp WHERE s IN (SELECT s FROM entry WHERE cate=(SELECT cate FROM entry WHERE s='$s')) AND r='$r' AND m='99'";
            $content = ($conn->query($query));

            if ($content->num_rows > 0) { // next sail found, insert (r,99,"cate",t) as first sail
                $rows = $content->fetch_all(MYSQLI_ASSOC);
                $t = $rows[0]['mint'];

                if ($t !=0) {
                    $query = "INSERT INTO stamp (r,m,s,t) VALUES ('$r','99',(SELECT cate FROM entry WHERE s='$s'),'$t')";
                    $content = ($conn->query($query));
                    return 3; // next sail found, inserted
                }
            }
            return 2; // stamp (r,99,"cate",t) deleted
        }
    }
    return 1; // if stamp (r,m,s,t) found, deleted
}

function StampFlag($conn,$r,$m,$s) {
    $query = "SELECT COUNT(s) as num FROM stamp WHERE  r='$r' AND m='$m' AND s='$s'";
    $content = ($conn->query($query));
    $row = $content->fetch_assoc();
    
    if ($row["num"] == 0) { return 'gainsboro';
    } else if ($row["num"] == 1) { return 'mediumaquamarine';
    } else if ($row["num"] == 2) { return 'orange';
    } else if ($row["num"] == 3) { return 'crimson';
    } else if ($row["num"] == 4) { return 'mediumpurple';
    } else { return 'steelblue';
    }
}

?>