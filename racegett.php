<?php

include 'dbconn.php';
$conn = OpenCon();
$r = CurrR($conn);
$timeArray = array();

$t = StampTime($conn,50,50,"bOffset");   // check time offset
if (!empty($t)) {
    $offset = end($t);
    if ($offset > 90000000) { $offset = -($offset - 90000000); }
    $timeArray[] = array("name" => "bOffset", "t" => $offset/1000);
} else {
    $timeArray[] = array("name" => "bOffset", "t" => "0");
}

if ($r != 0) { 
    $t = StampTime($conn,$r,50,"bLimits");   // check time limits
    $timeArray[] = array("name" => "bLimits", "t" => end($t)/60/1000);
    $t = StampTime($conn,$r,50,"bCutoff");   // check cutoff time
    $timeArray[] = array("name" => "bCutoff", "t" => end($t)/60/1000);

    $rRoute = PrepareTable($conn,"route"," WHERE r='" . $r . "' ORDER BY gp"); 
    foreach ($rRoute as $Route) { //foreach $Route['gp']
        $t = StampTime($conn,$r,91,$Route['gp']);   // check any gp Started
        if (!empty($t)) {
            $timeArray[] = array("name" => "timeS_" . $Route['gp'], "t" => TimeHHMMSS(end($t)));

            $num = NumOfCateInGp ($conn,$Route['gp']);
            if ($num) {
                $gpGrade = PrepareTable($conn,"grade"," WHERE gp='" . $Route['gp'] . "'");
                foreach ($gpGrade as $Grade) { //foreach $Grade['cate']
                    $t = StampTime($conn,$r,99,$Grade['cate']);   // check any cate Finished
                    if (!empty($t)) {
                        $timeArray[] = array("name" => "timeF_" . str_replace(" ", "_", $Grade['cate']), "t" => TimeHHMMSS(end($t)));
                    } else {
                        $timeArray[] = array("name" => "timeF_" . str_replace(" ", "_", $Grade['cate']), "t" => "");
                    }

                    $n = NumOfSailFin ($conn,$r,$Grade['cate'],'DNC') +
                         NumOfSailFin ($conn,$r,$Grade['cate'],'OCS') +
                         NumOfSailFin ($conn,$r,$Grade['cate'],'DNS') +
                         NumOfSailFin ($conn,$r,$Grade['cate'],'RET') +
                         NumOfSailFin ($conn,$r,$Grade['cate'],'DSQ');
                    $timeArray[] = array("name" => "numOUT_" . str_replace(" ", "_", $Grade['cate']), "t" => $n);
                    $n = NumOfSailFin ($conn,$r,$Grade['cate'],'DNF') +
                         NumOfSailFin ($conn,$r,$Grade['cate'],'NSC');
                    $timeArray[] = array("name" => "numDNF_" . str_replace(" ", "_", $Grade['cate']), "t" => $n);
                    $n = NumOfSailFin ($conn,$r,$Grade['cate'],'FIN');
                    $timeArray[] = array("name" => "numFIN_" . str_replace(" ", "_", $Grade['cate']), "t" => $n);
                }
            }
        } else {
            $timeArray[] = array("name" => "timeS_" . $Route['gp'], "t" => "");
        }
    }
} 

echo json_encode($timeArray);
CloseCon($conn);

?>