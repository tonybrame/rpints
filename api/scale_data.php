<?php

require_once __DIR__.'/../admin/includes/conn.php';

// Keep this API Key value to be compatible with the ESP32 code provided in the project page. 
// If you change this value, the ESP32 sketch needs to match
$api_key_value = "ckXfQi9CPAb3BzVGt";

$api_key= $scaleId = $weight = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $api_key = test_input($_POST["api_key"]);
    if($api_key == $api_key_value) {

    	global $mysqli;

        $scaleId = test_input($_POST["scale_id"]);
        $weight = test_input($_POST["weight"]);
        
        //check to see if there is a keg assigned... 

	    $sql = "SELECT kegId FROM scales WHERE id = " . $scaleId;
        $kegId = executeQueryWithSingleResult($sql);

        if($kegId != null)
        {

            $sql = "INSERT INTO kegScaleWeights (kegId, scaleId, weightReading, readingDate)
            VALUES (" . $kegId . ", " . $scaleId . ", " . $weight . ", NOW())";
            
            if(!executeNonQuery($sql))
                return;
            
            //beer is 8.34 lb/gallon. Need to subtract original weight from current, then divide to get remaining gallons
            $sql = "SELECT emptyWeight FROM kegs WHERE id = " . $kegId;
            $emptyWeight = executeQueryWithSingleResult($sql);

            $currentAmount = ($weight - $emptyWeight) / 8.34;

            $sql = "UPDATE kegs SET weight = " . $weight . ", currentAmount = " . $currentAmount . " WHERE id = " . $kegId;

            if(!executeNonQuery($sql))
                return;

            echo "Weight recorded successfully";
        }
        else
        {
            //no keg is set, throw away the data.... maybe we should log something :shrug:
            echo "No keg assigned to scale: " . $scaleId;
        }
    }
    else {
        echo "Wrong API Key provided .";
    }

}
else {
    echo "No data posted with HTTP POST.";
}

function executeNonQuery($sql){
    global $mysqli;

    $qry = $mysqli->query($sql);
    if($mysqli->error != ""){
        echo "Cannot Execute Query: " . $sql . "<br/>SQL Error: " . $mysqli->error;
        return false;
    }
    return true;
}

function executeQueryWithSingleResult($sql){

    global $mysqli;
    
    $qry = $mysqli->query($sql);

    if($mysqli->error != ""){
        echo "Cannot Execute Query: " . $sql . "<br/>SQL Error: " . $mysqli->error;
        return null;
    }
    
    $dbObject = null;
    if($qry && $i = $qry->fetch_array()){

        if( isset($i[0]) )
            $dbObject = $i[0];

    }
    return $dbObject;
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>