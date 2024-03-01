<?php

 header("Access-Control-Allow-Origin: *");

require_once __DIR__.'/../includes/config.php';

$api_key = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {

	$taps = array();
	$co2Details = array();
    
    $mysqli = db();		

    $sql =  "SELECT weight,emptyWeight,maxWeight FROM gasTanks WHERE gasTankStatusCode = 'DISPENSING' LIMIT 1";
    $qry = $mysqli->query($sql);

    while($b = mysqli_fetch_array($qry))
    {
        $emptyWeight = $b['emptyWeight'];
        $co2Details = array(
            "maxFill" => $b['maxWeight'] - $emptyWeight,
            "currentFill" => $b['weight'] - $emptyWeight
        );
    }

    $headerText = executeQueryWithSingleResult("SELECT configValue FROM config WHERE configName = 'headerText'");

    $sql =  "SELECT * FROM vwGetActiveTaps";
    $qry = $mysqli->query($sql);
    while($b = mysqli_fetch_array($qry))
    {
        $beeritem = array(
            "tap" => $b['tapNumber'],
            "color" => $b['srm'],
            "brewery" => $b['breweryName'],
            "breweryImage" => $b['breweryImageUrl'],
            "beerName" => $b['name'],
            "abv" => $b['abv'],
            "og" => $b['og'],
            "maxFill" => $b['startAmount'],
            "currentFill" => $b['remainAmount'],
            "fillScale" => "gallon",
		"active" => true
        );
        
        array_push($taps, $beeritem);
    }

    $returnValue = array();
    
    $returnValue["breweryName"] = $headerText;
    $returnValue["co2"] = $co2Details;
    $returnValue["taps"] = $taps;

    echo json_encode($returnValue);

}


function executeQueryWithSingleResult($sql){

    $mysqli = db();		
    
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

?>