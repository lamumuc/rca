<?php
	include 'dbconn.php';
	$conn = OpenCon();		 //echo "Connected";
    $r = $_POST["r"];
    if ($_POST["m"] < 100) {
        $m = $_POST["m"];
        $cate = "";
    } else {
        $m = 0;
        $cate = $_POST["m"];
    }
?>

<table id="tTimeView">
    <tr><td><table class="table table-bordered table-striped text-center"><?php
    if ($m == 0) {
        // Get path of gp
        $query = "SELECT route.path, route.gp FROM route JOIN grade ON route.gp = grade.gp WHERE route.r='$r' AND grade.cate = '$cate'";
        $content = $conn->query($query);
        $row = $content->fetch_assoc();
        $gppath = $row['path'];
        $gp = $row['gp'];

        // Get start time of gp
        $query = "SELECT t FROM stamp WHERE r='$r' AND m=91 AND s='$gp'";
        $content = $conn->query($query);
        if ($content->num_rows > 0) {
            $row = $content->fetch_assoc();
            $startt = $row['t'];
        } else {
            $startt = 0;
        }

        echo '<tr style="background-color: darkgray;"><td colspan="2">' . $cate. '</td>';
        echo '<td colspan="' . strlen($gppath)/2 . '">Route [ ' . $gppath. ' ]</td>';
        echo '<td colspan="' . strlen($gppath)/2+2 . '">Start Time  [ ' . TimeHHMMSSXXX($startt). ' ]</td></tr><tr></tr>';
        
        // Split the path into columns
        echo '<tr><th>Name</th><th>Sail</th>';
        for ($i = 0; $i < strlen($gppath); $i++) {
            echo '<th>Mark ' . $gppath[$i] . '</th>';
        }
        echo '<th>Position</th></tr>';

        // Get s from result
        $query = "SELECT s,`rank`,code FROM result WHERE r='$r' AND s IN (SELECT s FROM entry WHERE cate='$cate') ORDER BY `rank`";
        $content_result = $conn->query($query);
        $result = $content_result->fetch_assoc();

        // Get sailed path of each s
        while ($result) {
            $s = $result['s'];

            $query = "SELECT name FROM entry WHERE s = '$s'";
            $content = $conn->query($query);
            $row = $content->fetch_assoc();
            echo '<tr><td style="text-align:left; ">'.$row['name'].'</td>';
            
            $query = "SELECT GROUP_CONCAT(IF(m = 99, 'F', CAST(m AS CHAR)) ORDER BY t ASC SEPARATOR '') AS sailed FROM stamp WHERE r = '$r' AND s = '$s'";
            $content = $conn->query($query);
            $row = $content->fetch_assoc();
            // echo '<td>'.$s.'<br>[ '.$row['sailed'].' ]</td><td>';

            $sailed = PrepareTable($conn,"stamp"," WHERE r='$r' AND s='$s' ORDER BY t");


            echo '<td>'.$s.'<br><button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalTime" 
            style="font-size: 0.8vw;" title="Edit time stamp for this Sail"
            onclick="TimeButtonEdit(\''. $r .'\',\''. $cate .'\',\''. $s .'\',\''. $gppath .'\',\''. $startt .'\','. htmlspecialchars(json_encode($sailed,true), ENT_QUOTES) .');" 
            data-toggle="tooltip" data-placement="right" data-html="true">'.$row['sailed'].'</button>'.'</td><td>';

            $i = 0; //gppath counter  if matched i++,j++
            $j = 0; //sailed counter  if not matched j++
            $k = 0; //table column counter
            
            // Handle if sailed path = 0
            if (count($sailed) == 0) {
                while ($gppath[$i] != 'F') {
                    if ($i == strlen($gppath)) { break; }
                    $i++;
                    echo '</td><td>';
                }
            }

            // Compare $gppath and $sailed
            while ($i < strlen($gppath) && $j < count($sailed)) {
                //echo $i.$j.$k;
                
                if ($sailed[$j]['m'] == '99' && $j == count($sailed)-1) { // Handle Mark F and boxes to skip
                    while ($gppath[$i] != 'F') {
                        if ($i == strlen($gppath)) { break; }
                        $i++;
                        echo '</td><td>';
                    }
                    echo TimeHHMMSSXXX($sailed[$j]['t']-$startt) . '<br>';
                } else if ($j == count($sailed)-1) {                    // Handle last sailed mark and boxes to skip
                    if ($gppath[$i] == $sailed[$j]['m']) {   // Print matched Mark Time
                        $i++;
                        echo TimeHHMMSSXXX($sailed[$j]['t']-$startt) . '</td><td>';
                    } else {
                        echo '(M' . $sailed[$j]['m'] . ' ' . TimeHHMMSSXXX($sailed[$j]['t']-$startt) . ' )<br>';
                    }
                    while ($gppath[$i] != 'F') {
                        if ($i == strlen($gppath)) { break; }
                        $i++;
                        echo '</td><td>';
                    }
                } else if ($gppath[$i] == $sailed[$j]['m']) {   // Print matched Mark Time
                    $i++;
                    echo TimeHHMMSSXXX($sailed[$j]['t']-$startt) . '</td><td>';
                } else {                                        // Print mismatched Mark Time
                    $k++;
                    if ($sailed[$j]['m'] === '99') {
                        echo '(F ' . TimeHHMMSSXXX($sailed[$j]['t'] - $startt) . ')<br>';
                    } else {
                        echo '(M' . $sailed[$j]['m'] . ' ' . TimeHHMMSSXXX($sailed[$j]['t'] - $startt) . ' )<br>';
                    }
                }
                $j++;
            }
            echo '</td>';
            
            // Print the Rank column
            if ($result['code'] != 'FIN') {
                echo '<td>'.$result['rank'].' ('.$result['code'].')</td>';
            } else {
                echo '<td>'.$result['rank'].'</td>';
            }
            echo '</tr>';
            $result = $content_result->fetch_assoc();
        }
    } else {
        $query = "SELECT s, t FROM stamp WHERE r='$r' AND m ='$m' AND s IN (SELECT s FROM entry) ORDER BY t";
        $content = $conn->query($query);
        $row = $content->fetch_assoc();

        echo '<tr><th>Sail</th><th>Time</th></tr>';
        while ($row) {
            echo '<tr>';
            echo '<td>'.$row['s'].'</td>';
            echo '<td>'.TimeHHMMSSXXX($row['t']).'</td>';
            echo '</tr>';
            $row = $content->fetch_assoc();
        }
    }?></table></td></tr>
</table>

<?php
    CloseCon($conn);
?>
