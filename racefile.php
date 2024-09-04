<?php

include 'dbconn.php';
$conn = OpenCon();
$table = isset($_POST["table"]) ? $_POST["table"] : 'NULL';
$value = isset($_POST["value"]) ? $_POST['value'] : 'NULL';

$retryLimit = 3; // Set the maximum number of retries
$retryCount = 0; // Initialize the retry count

while ($retryCount < $retryLimit) {
    try {
        if ($value == 'DELETE') {
            $query = "DELETE FROM " . $table;
        } else {
            if ($table == 'entry') {
                $query = "INSERT INTO " . $table . " (cate,name,s) VALUES ". $value;
            } else if ($table == 'grade') {
                $query = "INSERT INTO " . $table . " (gp,cate,py) VALUES " . $value;
            } else if ($table == 'route') {
                $query = "INSERT INTO " . $table . " (mode,gp,path) VALUES" . $value;
            }
        }
        // echo $query;
        $content = mysqli_query($conn, $query);
        break; // Break out of the retry loop if the query is successful
    } catch (mysqli_sql_exception $e) {
        $retryCount++;
        if ($retryCount === $retryLimit) {
            throw $e; // Throw the exception if the maximum number of retries is reached
        }
        sleep(1); // Wait for a short period before retrying
    }
}

CloseCon($conn);

?>