<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
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

if($_GET['isocode2']){

    $isocode2 = $_GET['isocode2'];

    $sql = "SELECT * FROM kml_overlay WHERE isocode2 IN ('".$isocode2."')";
    $result = $conn->query($sql);

    if ($result){

        $row = $result->fetch_assoc();
        $xml = simplexml_load_string($row['kml'], "SimpleXMLElement", LIBXML_NOCDATA);

        $json = json_encode($xml);

        $data = json_decode($json,TRUE);
        $out =[];
        $out['type'] = 'FeatureCollection';

        $keyindex = -1;
        $placemarkobj=[];
        $rawdata = $data['Document']['Folder']['Placemark'];
        if(array_key_exists('name',$rawdata)){
            $placemarkobj[0] = $rawdata;
        } else {
            $placemarkobj = $rawdata;
        }

        foreach($placemarkobj as $key => $placemark) {

            $keyindex++;
            $out['features'][$keyindex]['type'] = 'Feature';
            $out['features'][$keyindex]['geometry']['type'] = 'Polygon';
            $out['features'][$keyindex]['properties']['country_iso2'] = $isocode2;
            $out['features'][$keyindex]['properties']['country_label_top'] = $data['Document']['Folder']['name'];
            //$out['features'][$keyindex]['properties']['country_label'] = $placemark['name'];

            if(array_key_exists('MultiGeometry',$placemark)){

                foreach ($placemark['MultiGeometry'] as  $geometry){
                    //Key is Polygon
                    foreach($geometry as $keygeo => $polygon){
                        if(array_key_exists('outerBoundaryIs', $polygon)){
                            $out['features'][$keyindex]['geometry']['coordinates'][] = createCoordsArray($polygon['outerBoundaryIs']['LinearRing']['coordinates']);
                        }
                        if(array_key_exists('innerBoundaryIs', $polygon)){
                            $out['features'][$keyindex]['geometry']['coordinates'][] = createCoordsArray($polygon['innerBoundaryIs']['LinearRing']['coordinates']);
                        }
                    }
                }

            } else {

                $polygon = $placemark['Polygon'];

                if(array_key_exists('outerBoundaryIs', $polygon)){
                    $out['features'][$keyindex]['geometry']['coordinates'][] = createCoordsArray($polygon['outerBoundaryIs']['LinearRing']['coordinates']);
                }
                if(array_key_exists('innerBoundaryIs', $polygon)){
                    $out['features'][$keyindex]['geometry']['coordinates'][] = createCoordsArray($polygon['innerBoundaryIs']['LinearRing']['coordinates']);
                }

            }

        }

        $encoded_out = json_encode($out);
         $fp = fopen("geojson/".$isocode2.'.json', 'w');
         fwrite($fp, $encoded_out);
         fclose($fp);

        header('Content-Type: application/json');
        echo $encoded_out;



    } else {

        echo ("No result. iso");
    }

    $conn->close();

} /*else {

    $sql = "SELECT * FROM kml_overlay LIMIT 10 OFFSET 15";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $xml = simplexml_load_string($row['kml'], "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $data = json_decode($json,TRUE);



        }
    } else {
        echo ("No result. all");
    }


}*/

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

