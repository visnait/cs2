<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18-Jul-17
 * Time: 9:02 AM
 * Insert Time Line Event
 */



/*if(isset($_GET['carId'])){
    $carId = $_GET['carId'];
    $basurl = '';

    $servername = "localhost";
    $username = "u891076453_car";
    $password = "QAZwsx123**";
    $dbname = "u891076453_car";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM tl_cars WHERE uid=".intval($carId);
    $result = $conn->query($sql);

    if ($result){
        $row = $result->fetch_assoc();
        //echo "Car: " . $row["manufacturer"]. " - type: " . $row["type"];

        $data = [];

        $data['scale'] = 'human';
        $data['title']['background']['color'] = '#ddd';
        $data['title']['background']['opacity'] = '0.5';
        $data['title']['background']['url'] = 'data/images/IMG_20140413_132817.jpg';

        echo json_encode($data);


    } else {
        echo "Car not found";
    }
    $conn->close();


} else {
    echo 'Not valid parameter';
}*/
