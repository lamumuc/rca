<?php
	include 'dbconn.php';
	$conn = OpenCon();		// echo "Connected";
    $m = $_POST["m"];
    if ($m == 0) {                                                                  // if m=0, return empty table
        return 0;
    }
?>

<table id="tMark">
<?php
    $rs = 4;
	$zs = 4;
    $r = CurrR($conn);
    $rRoute = PrepareTable($conn,"route"," WHERE r='" . $r . "' ORDER BY gp");    // fetch gp
    foreach ($rRoute as $Route) { 
    if ($Route['gp'] > "") {?>
        <tr>
            <th colspan="<?php echo $rs+1 ?>"><?php echo $Route['gp']?></th>
        </tr>
        <?php
        $gpGrade = PrepareTable($conn,"grade"," WHERE gp='" . $Route['gp'] . "' ORDER BY py");	// fetch cate in each gp
        foreach ($gpGrade as $Grade) { ?>
            <tr> <?php																// fetch s in each cate
                $cateEntry = PrepareTable($conn,"entry"," WHERE cate='" . $Grade['cate'] . "' ORDER BY 's'");?>
                <td rowspan="<?php ceil(sizeof($cateEntry) / $rs) ?>"><?php echo $Grade['cate'] ?></td>
            <?php
            $i = 0;
            if (sizeof($cateEntry) == 0) {
                $n = $rs;
                while (--$n >= 0) {?>
                    <td></td><?php  												// fill blank cells
                } 
            }
            foreach ($cateEntry as $Entry) { ?>
                <td><?php 
                $flag = StampFlag($conn,$r,$m,$Entry['s']);
                echo "<button id=\"b" . $Entry['s'] . "\"style=\"width: 100%; 
					font-size: " . $zs . "vw; height: " . $zs*30 . "px; background-color: ". $flag .";\" 
                    oncontextmenu=\"MarkButtonCMenu('" . $Entry['s'] . "')\" 
                    ontouchstart=\"event.preventDefault(); MarkButtonPress('" . $Entry['s'] . "')\" 
                    ontouchend=\"event.preventDefault(); MarkButtonLease('" . $Entry['s'] . "')\" 
                    onmousedown=\"MarkButtonPress('" . $Entry['s'] . "')\" 
                    onmouseup=\"MarkButtonLease('" . $Entry['s'] . "')\" 
                    >" . $Entry['s'] . "</button>"; ?>
                </td><?php 
                $i++;
                if (($i != sizeof($cateEntry)) && ((int)($i%$rs) == 0)) { ?>
                    </tr><tr><td></td> <?php  										// jump to new row
                }
            }
            $n = $rs-sizeof($cateEntry)%$rs;
            while ($n != $rs && --$n >= 0) {?>
                <td></td><?php  													// fill blank cells
            } 
        } ?>
        </tr><?php
    } 
    } ?>
</table>

<?php
    CloseCon($conn);
?>
