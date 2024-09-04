<?php
	include 'dbconn.php';
	$conn = OpenCon();		// echo "Connected";
    $rC = CurrR($conn);
?>

<table id="tRaceComp">
<?php for ($r = $rC-1; $r >= $rC-$rC+1; $r--) { 
    $rRoute = PrepareTable($conn,"route"," WHERE r='" . $r . "' ORDER BY gp"); ?>
    <tr><td><table class="table table-dark table-striped">
    <tr><th class="text-center" colspan="5"><h4>Race <?php echo $r ?>&nbsp; <?php echo $rRoute[0]['mode'] ?></h4></th></tr>
    <tr>
    <th scope="col" colspan="1">Group  [Route]</th> <?php
        foreach ($rRoute as $Route) { 
            $num = NumOfCateInGp ($conn,$Route['gp']);
            if ($num) { ?>
                <td class="text-center" colspan="<?php echo $num ?>"><?php echo $Route['gp'] ?>
                &nbsp; [<?php echo $Route['path'] ?>]</td> <?php 
            }
        } ?>
    </tr>
    <tr>
        <th scope="col" colspan="1">Start Time</th> <?php
        foreach ($rRoute as $Route) { 
            $num = NumOfCateInGp ($conn,$Route['gp']);
            if ($num) {
                $t = StampTime($conn,$r,91,$Route['gp']);
                if ($t !== null && count($t) > 0) { ?>
                    <td class="text-center" colspan="<?php echo $num ?>"><?php echo TimeHHMMSS(end($t)); ?></td> <?php 
                } else { ?>
                    <td class="text-center" colspan="<?php echo $num ?>">HH:MM:SS</td> <?php 
                }
            }
        }?>
    </tr>
    <tr>
        <th scope="col" colspan="1">Class<br>(PY)</th> <?php
        foreach ($rRoute as $Route) { 
            $num = NumOfCateInGp ($conn,$Route['gp']);
            if ($num) { 
                $gpGrade = PrepareTable($conn,"grade"," WHERE gp='" . $Route['gp'] . "' ORDER BY py");
                foreach ($gpGrade as $Grade) { ?>
                    <td class="text-center" colspan="1"><?php echo $Grade['cate'] ?><br><?php if ($Grade['py'] !== "0") {echo $Grade['py'];} ?></td> <?php 
                }
            }
        }?>
    </tr>
    <tr>
        <th scope="col" colspan="1">Count
        <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalCode" style="font-size: 0.8vw;"
            onclick='document.getElementById("modalCodeTitle").innerHTML = "Enter Code for Race <?php echo $r ?>"; RaceButtonCode(<?php echo $r ?>);'
            data-toggle="tooltip" data-placement="right" title="Enter DNC/OCS/DNS/RET/DSQ for this Race">Code</button>
        </th> <?php
        foreach ($rRoute as $Route) { 
            $num = NumOfCateInGp ($conn,$Route['gp']);
            if ($num) { 
                $gpGrade = PrepareTable($conn,"grade"," WHERE gp='" . $Route['gp'] . "' ORDER BY py");
                foreach ($gpGrade as $Grade) { 
                    $numS = NumOfSInCate ($conn,$Grade['cate']);
                    $numF = NumOfSailFin ($conn,$r,$Grade['cate'],'FIN'); ?>
                    <td class="text-center" colspan="1"><?php echo $numF ?> /<?php echo $numS ?></td> <?php 
                }
            }
        }?>
    </tr>
    <tr></tr>
    </table></td></tr> <?php 
}?>
</table>

<?php
    CloseCon($conn);
?>
