<?php
	include 'dbconn.php';
	$conn = OpenCon();		 //echo "Connected";
    $rC = CurrR($conn);
    $v = $_POST["v"];
?>

<table id="tTimeTool">
    <tr><td><div class="btn-toolbar justify-content-between">
        <div class="btn-group btn-group-toggle" data-toggle="buttons"><?php
            for ($i = 1; $i < $rC; $i++) {
                echo '<label class="btn btn-sm btn-info">';
                echo '<input type="radio" name="vRace" value="' . $i . '" autocomplete="off">Race ' . $i . '';
                echo '</label>';
            }
            echo '<label class="btn btn-sm btn-info active">';
            echo '<input type="radio" name="vRace" value="' . $i . '" autocomplete="off" checked>Race ' . $rC . '';
            echo '</label>';
        ?></div>
    </div></td></tr>
    <tr><td><div class="btn-toolbar justify-content-end">
        <div class="btn-group btn-group-toggle" data-toggle="buttons"><?php
            if ($v == "viewMark") {
                $query = "SELECT DISTINCT m FROM stamp ORDER BY m";
                $content = $conn->query($query);
                $row = $content->fetch_assoc();
                while ($row) {
                    if ($row['m'] < 10) {
                        echo '<label class="btn btn-sm btn-info">';
                        echo '<input type="radio" name="vMark" value="' . $row['m'] . '" autocomplete="off">Mark ' . $row['m'] . '';
                        echo '</label>';
                    }
                    $row = $content->fetch_assoc();
                }
                echo '<label class="btn btn-sm btn-info active">';      // checked the last Mark
                echo '<input type="radio" name="vMark" value="99" autocomplete="off" checked>Mark F';
                echo '</label>';
            } else {
                $query = "SELECT DISTINCT cate FROM grade ORDER BY gp,py";
                $content = $conn->query($query);
                $row = $content->fetch_assoc();

                echo '<label class="btn btn-sm btn-info active">';      // checked the first cate
                echo '<input type="radio" name="vMark" value="' . $row['cate'] . '" autocomplete="off" checked>' . $row['cate'] . '';
                echo '</label>';
                $row = $content->fetch_assoc();

                while ($row) {
                    echo '<label class="btn btn-sm btn-info">';
                    echo '<input type="radio" name="vMark" value="' . $row['cate'] . '" autocomplete="off">' . $row['cate'] . '';
                    echo '</label>';
                    $row = $content->fetch_assoc();
                }
            }
        ?></div>
    </div></td></tr>
</table>

<?php
    CloseCon($conn);
?>
