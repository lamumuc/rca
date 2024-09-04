<?php
	include 'dbconn.php';
	$conn = OpenCon();		// echo "Connected";
    $rC = CurrR($conn);
    $rE = floor(($rC - 4) / 8) + 1;     // start from r=4, exclude 1 r every 8 r
?>

<table id="tRank"><?php
    $rRoute = PrepareTable($conn,"route"," WHERE r='" . $rC . "' ORDER BY gp");    // fetch gp
    foreach ($rRoute as $Route) { 
        if ($Route['gp'] > "") {
            $gpGrade = PrepareTable($conn,"grade"," WHERE gp='" . $Route['gp'] . "' ORDER BY py");	// fetch cate in each gp
            foreach ($gpGrade as $Grade) { ?>
                <tr><td><table class="table table-bordered table-striped text-center ">
                    <th colspan="<?php echo $rC + 4; ?>"><?php echo $Grade['cate']?></th></tr><?php

                    $sql = "SELECT entry.name, result.s, ";
                    // Generate the SQL query with the appropriate number of code_$i and rank_$i
                    for ($i = 1; $i <= $rC; $i++) {
                        $sql .= "MAX(CASE WHEN r = $i THEN code END) AS code_$i, 
                                MAX(CASE WHEN r = $i THEN `rank` END) AS rank_$i, ";
                    }

                    // score = sum of ranks with worst $rE excluded
                    $sql .= "t1.score AS score, CONCAT(";
                    
                    // in case of same score, sort by rank
                    // for W only , compare excluded rank, from best rank to worst rank
                    if (substr($Route['gp'], 0, 1) === "W") {
                        $sql .= "SUBSTRING(GROUP_CONCAT(LPAD(`rank`, 2, '0') ORDER BY `rank` ASC SEPARATOR ''),($rC-$rE)*2+1,$rE*2),";
                    }
                    // compare included ranks, from best rank to worst rank
                    // compare all ranks, from last race to first race
                    $sql .= "SUBSTRING(GROUP_CONCAT(LPAD(`rank`, 2, '0') ORDER BY `rank` ASC SEPARATOR ''),1,($rC-$rE)*2),
                                   GROUP_CONCAT(LPAD(`rank`, 2, '0') ORDER BY r DESC SEPARATOR '')) AS ranksort
                            FROM result 
                            JOIN entry ON entry.s = result.s
                            JOIN (SELECT s, SUM(`rank`) AS score FROM (
                                SELECT s, `rank`,
                                    ROW_NUMBER() OVER (PARTITION BY s ORDER BY `rank` ASC) AS row_num
                                FROM result
                            ) AS subquery WHERE row_num <= $rC-$rE GROUP BY s) AS t1 ON t1.s = result.s
                            WHERE entry.cate='{$Grade['cate']}'
                            GROUP BY result.s ORDER BY score ASC, ranksort ASC";

                    // Execute the SQL query and fetch the results
                    $content = $conn->query($sql);
                    $row = $content->fetch_assoc();

                    // Output the results as an HTML table ?>
                    <tr><th>Rank</th><th>Name</th><th>Sail</th><?php
                    for ($i = 1; $i <= $rC; $i++) {
                        echo "<th>Race $i</th>";
                    } ?>
                    <th>Score</th><!-- <th>RankSort</th> --></tr><?php

                    $post = 1;
                    while ($row) {
                        echo '<tr>';
                        echo '<td>' . $post . '</td>';
                        echo '<td style="text-align:left; ">' . htmlspecialchars($row['name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['s']) . '</td>';
                        for ($i = 1; $i <= $rC; $i++) {
                            $code = $row["code_$i"];
                            $rank = $row["rank_$i"];
                            if ($code == "FIN") {
                                echo '<td>' . htmlspecialchars($rank) . '</td>';
                            } else {
                                echo '<td>' . htmlspecialchars("$rank ($code)") . '</td>';
                            }
                        }
                        echo '<td>' . htmlspecialchars($row['score']) . '</td>';
                        // echo '<td>' . htmlspecialchars($row['ranksort']) . '</td>';
                        echo '</tr>';
                        $row = $content->fetch_assoc();
                        $post++;
                    } ?>
                </table></td></tr><?php
            }
        }
    }?>
</table>

<?php
    CloseCon($conn);
?>
