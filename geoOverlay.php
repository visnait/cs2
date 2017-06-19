<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

if($_GET['isocode2']){
    $isocode2 = $_GET['isocode2'];
    $basurl = '';

    $servername = "mysql.hostinger.sk";
    $username = "u891076453_geo";
    $password = "QAZwsx123**";
    $dbname = "u891076453_geo";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM kml_overlay WHERE isocode2 IN ('".$isocode2."')";
    $result = $conn->query($sql);

    if ($result){
        $row = $result->fetch_assoc();

        $xml = simplexml_load_string($row['kml'], "SimpleXMLElement", LIBXML_NOCDATA);
       /* header('Content-Type: application/xml');
        echo json_encode($xml);
        exit();*/

        $json = json_encode($xml);
        $dataArr = json_decode($json,TRUE);

        $data = [];
        $place= [];
        $data['type'] = 'FeatureCollection';
        $folder = $dataArr['Document']['Folder'];
        $placeId = 0;

        if(!empty($folder)){
            //echo ("Is array: " .is_array($folder['Placemark']));
          /*  header('Content-Type: application/json');
            echo json_encode($folder);
            exit;*/


            foreach ($folder['Placemark'] as $place){

                /*header('Content-Type: application/json');
                echo json_encode($place);
                exit;*/


                $data['features'][$placeId]['type'] = 'Feature';
                 $data['features'][$placeId]['geometry']['type'] = 'Polygon';
                 //$data['features'][$placeId]['properties']['country_iso2'] = $isocode2;
                 //$data['features'][$placeId]['properties']['country_label_top'] = $folder['name'];
                 //$data['features'][$placeId]['properties']['country_label'] = $place['name'];

                 if(array_key_exists('MultiGeometry',$place)){
                     $currentMulti = $place['MultiGeometry'];

                     foreach ($currentMulti['Polygon'] as $igeo => $geometry){
                         if(array_key_exists('outerBoundaryIs',$geometry)){
                             $data['features'][$placeId]['geometry']['coordinates'][$igeo] = createCoordsArray($geometry['outerBoundaryIs']['LinearRing']['coordinates']);
                         }
                         if(array_key_exists('innerBoundaryIs',$geometry)){
                             $data['features'][$placeId]['geometry']['coordinates'][$igeo] = createCoordsArray($geometry['innerBoundaryIs']['LinearRing']['coordinates']);
                         }
                     }
                 } else {
                     if(array_key_exists('outerBoundaryIs',$place['Polygon'])) {
                         $data['features'][$placeId]['geometry']['coordinates'][0] = createCoordsArray($place['Polygon']['outerBoundaryIs']['LinearRing']['coordinates']);
                     }
                     if(array_key_exists('innerBoundaryIs',$place['Polygon'])) {
                         $data['features'][$placeId]['geometry']['coordinates'][0] = createCoordsArray($place['Polygon']['innerBoundaryIs']['LinearRing']['coordinates']);
                     }
                 }

                 $placeId +=1;
             }
        }

        header('Content-Type: application/json');
        echo json_encode($data);


    } else {
        echo "";
    }
    $conn->close();


} else {
    echo 'Not valid parameter';
}

function createCoordsArray($source){

    $coordlist=[];
    $coordArr = explode(" ",$source);

    foreach ($coordArr as $i => $coordline){
        $coords = explode(',',$coordline);
        $coordlist[$i][0] = (float)$coords[0];
        $coordlist[$i][1] = (float)$coords[1];
    }

    return  $coordlist;

}