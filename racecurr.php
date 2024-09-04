<?php
	include 'dbconn.php';
	$conn = OpenCon();		// echo "Connected";
    $r = CurrR($conn);
?>

<table id="tRaceCurr">
<?php if ($r != 0) { 
    $rRoute = PrepareTable($conn,"route"," WHERE r='" . $r . "' ORDER BY gp"); ?> 
    <tr><td><table class="table table-dark table-striped">
    <tr><th class="text-center" colspan="5"><h4>Race <?php 
                echo $r ?>&nbsp; <?php echo $rRoute[0]['mode'] ?>&nbsp; <?php
                if ($rRoute[0]['mode'] == "FD") {
                    $newmode = "RM";
                } else {
                    $newmode = "FD";
                }
                // button to change mode for r
                echo "<button id=\"racemode\" type=\"button\" class=\"btn btn-light btn-sm\" style=\"font-size: 0.8vw;\"
                    onclick=\"RaceButtonMode(" . $r . ",'" . $newmode . "')\"
                    data-toggle=\"tooltip\" title=\"Change Race Mode. Cannot be changed after race started+5min\">Mode</button>";  ?>&nbsp; <?php
                // button to delete race r
                echo "<button id=\"racedelr\" type=\"button\" class=\"btn btn-light btn-sm\" style=\"font-size: 0.8vw;\"
                    onclick=\"RaceButtonDel(" . $r . ")\"
                    data-toggle=\"tooltip\" title=\"Delete Current Race\">Del</button>"; ?></h4></th></tr>
    <tr>
        <th scope="col" colspan="1">Group  [Route]</th> <?php
        foreach ($rRoute as $Route) { 
            $num = NumOfCateInGp ($conn,$Route['gp']);
            if ($num) { ?>
                <td class="text-center" colspan="<?php echo $num ?>"><?php echo $Route['gp'] ?>
                &nbsp; <?php // button to change path for r / gp
                echo "<button  type=\"button\" class=\"btn btn-light btn-sm\" style=\"font-size: 0.8vw;\"
                    onclick=\"RaceButtonPath(" . $r . ",'" . $Route['mode'] . "','" . $Route['gp'] . "','" . $Route['path'] . "')\" 
                    data-toggle=\"tooltip\" title=\"Change Group Route. Cannot be changed after any sail finished\">" . $Route['path'] . "</button>"; ?>
                </td> <?php 
            }
        } ?>
    </tr>
    <tr>
        <th scope="col" colspan="1">Start Time<br>Lap Time 
        <button class="btn btn-sm btn-secondary" type="button" onclick="RaceButtonSync('bLimits')" id="bLimits"
        data-toggle="tooltip" data-placement="right" data-html="true" title="Set Time Limits for Current Race. Cannot be changed after first sail finished">#</button></th> <?php
        foreach ($rRoute as $Route) { //foreach gp
            $num = NumOfCateInGp ($conn,$Route['gp']);
            if ($num) { // get start time of r,m='S',s=gp , display it and calculate lap time ?>
                <td class="text-center" colspan="<?php echo $num ?>">
                <span id="timeS_<?php echo $Route['gp'] ?>" style="display:none;"></span>
                <span id="timeL_<?php echo $Route['gp'] ?>" style="display:none; font-size: 2vw;"></span>
                <button id="b<?php echo $Route['gp'] ?>" style="display:inline-block; font-size: 0.8vw;" type="button" class="btn btn-success btn-sm" 
                    oncontextmenu="document.getElementById('m').innerHTML = 'S'; MarkButtonCMenu('<?php echo $Route['gp'] ?>')" 
                    ontouchstart="event.preventDefault(); document.getElementById('m').innerHTML = 'S'; MarkButtonPress('<?php echo $Route['gp'] ?>')" 
                    ontouchend="event.preventDefault(); document.getElementById('m').innerHTML = 'S'; MarkButtonLease('<?php echo $Route['gp'] ?>')" 
                    onmousedown="document.getElementById('m').innerHTML = 'S'; MarkButtonPress('<?php echo $Route['gp'] ?>')" 
                    onmouseup="document.getElementById('m').innerHTML = 'S'; MarkButtonLease('<?php echo $Route['gp'] ?>')"
                    data-toggle="tooltip" data-placement="bottom" data-html="true" title="Set Start Time -10min~+30min from Now. Cannot be changed after race started+5min">START</button>
                </td> <?php 
            }
        }?>
    </tr>
    <tr>
        <th scope="col" colspan="1">First Sail<br>Cut-off 
        <button class="btn btn-sm btn-secondary" type="button" onclick="RaceButtonSync('bCutoff')" id="bCutoff"
        data-toggle="tooltip" data-placement="right" data-html="true" title="Set Cutoff Time for Class. Cannot be changed after first sail finished">#</button></th> <?php
        foreach ($rRoute as $Route) { //foreach gp
            $num = NumOfCateInGp ($conn,$Route['gp']);
            if ($num) {
                $gpGrade = PrepareTable($conn,"grade"," WHERE gp='" . $Route['gp'] . "' ORDER BY py");
                foreach ($gpGrade as $Grade) { //foreach cate ?> 
                    <td class="text-center">
                    <span id="timeF_<?php echo str_replace(" ", "_", $Grade['cate']) ?>" style="display:block;"></span>
                    <span id="timeC_<?php echo str_replace(" ", "_", $Grade['cate']) ?>" style="display:block; font-size: 2vw;"></span></td>
                    </td> <?php 
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
                    <td class="text-center"><?php echo $Grade['cate'] ?><br><?php if ($Grade['py'] !== "0") {echo $Grade['py'];} ?></td> <?php 
                }
            }
        }?>
    </tr>
    <tr>
        <th scope="col" colspan="1">DQ/NF/F<br>Count 
        <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalCode" style="font-size: 0.8vw;"
            onclick='document.getElementById("modalCodeTitle").innerHTML = "Enter Code for Race " + document.getElementById("r").innerHTML; RaceButtonCode(document.getElementById("r").innerHTML);'
            data-toggle="tooltip" data-placement="right" data-html="true" title="Enter DNC/OCS/DNS/RET/DSQ for Current Race">Code</button></th> <?php
        foreach ($rRoute as $Route) { 
            $num = NumOfCateInGp ($conn,$Route['gp']);
            if ($num) { 
                $gpGrade = PrepareTable($conn,"grade"," WHERE gp='" . $Route['gp'] . "' ORDER BY py");
                foreach ($gpGrade as $Grade) { 
                    $numS = NumOfSInCate ($conn,$Grade['cate']);?>
                    <td class="text-center">
                    <span id="numOUT_<?php echo str_replace(" ", "_", $Grade['cate']) ?>">0</span>/
                    <span id="numDNF_<?php echo str_replace(" ", "_", $Grade['cate']) ?>">0</span>/
                    <span id="numFIN_<?php echo str_replace(" ", "_", $Grade['cate']) ?>">0</span> =
                    <span id="numALL_<?php echo str_replace(" ", "_", $Grade['cate']) ?>"><?php echo $numS ?></span>
                    </td> <?php 
                }
            }
        }?>
    </tr></table></td></tr><?php 
} ?>
</table>

<?php
    CloseCon($conn);
?>
