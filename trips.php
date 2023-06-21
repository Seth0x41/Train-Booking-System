<?php
// Include the connectDb.php file
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require_once('connectDb.php');
session_start();

// if (isset($_POST['departure_station']) && isset($_POST['arrival_station']) && isset($_POST['departure_time'])) {
$departure_station = (int) $_POST["departure_station"];
$arrival_station = (int) $_POST["arrival_station"];
$userID = $_SESSION['user'];


// $sourceId = $departure_station; // Assuming Zag's station ID is 1
// $destinationId = $arrival_station; // Assuming Alex's station ID is 4


function findPath($departure_station, $arrival_station, $connection)
{
    // Create a queue to store the current path
    $queue = array();

    // Create a visited array to keep track of visited nodes
    $visited = array();

    // Enqueue the source node along with an empty path
    array_push($queue, array($departure_station, array()));

    // Mark the source node as visited
    $visited[$departure_station] = true;

    // Perform BFS
    while (!empty($queue)) {
        // Dequeue the front element
        $current = array_shift($queue);
        $node = $current[0];
        $path = $current[1];

        // Check if we reached the destination
        if ($node == $arrival_station) {
            // Add the destination to the path
            $path[] = $arrival_station;

            // Return the path
            return $path;
        }

        // Query the database for trips starting from the current node
        $query = "SELECT trips.trip_id, trips.arrival_station_id, stations.station_name, trips.departure_time 
                  FROM trips
                  INNER JOIN stations ON trips.arrival_station_id = stations.station_id
                  WHERE trips.departure_station_id = '$node'";
        $result = $connection->query($query);

        if ($result && $result->num_rows > 0) {
            // Fetch the destinations and enqueue them
            while ($row = $result->fetch_assoc()) {
                $destId = $row['arrival_station_id'];

                // Check if the destination node is not visited
                if (!isset($visited[$destId])) {
                    // Mark the destination as visited
                    $visited[$destId] = true;

                    // Enqueue the destination node with the updated path
                    $newPath = $path;
                    $newPath[] = array('station_id' => $node, 'trip_id' => $row['trip_id'], 'station_name' => $row['station_name'], 'departure_time' => $row['departure_time']);
                    array_push($queue, array($destId, $newPath));
                }
            }
        }
    }

    // No path found
    return null;
}

// Function to get the station name by ID
function getStationName($stationId, $connection)
{
    $query = "SELECT station_name FROM stations WHERE station_id = '$stationId'";
    $result = $connection->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['station_name'];
    }

    return null;
}


function getArrivalTime($arrivalStationId, $connection)
{
    $query = "SELECT arrival_time FROM trips WHERE arrival_station_id = '$arrivalStationId'";
    $result = $connection->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['arrival_time'];
    }

    return null;
}
function insertUserTrip($userID, $tripID, $connection)
{
    $query = "INSERT INTO user_trip (user_id, trip_id) VALUES ('$userID', '$tripID')";

    // Execute the query
    if ($connection->query($query) === TRUE) {
        echo "User ID and Trip ID inserted successfully.";
    } else {
        echo "Error inserting User ID and Trip ID: " . $connection->error;
    }
}

// Set the source and destination station IDs

// Find the path from Zag to Alex
$path = findPath($departure_station, $arrival_station, $conn);

if ($path) {
    // Convert station IDs to station names and departure times
    $pathInfo = array_map(function ($node) use ($conn) {
        $stationId = $node['station_id'];
        $stationName = getStationName($stationId, $conn);
        $departureTime = !empty($node['departure_time']) ? $node['departure_time'] : "";
        $tripId = $node['trip_id'];

        return array('station_name' => $stationName, 'departure_time' => $departureTime, 'trip_id' => $tripId);
    }, $path);



    // Remove the last element from the pathInfo array
    array_pop($pathInfo);

    if (isset($_POST['trip_id'])) {
        foreach ($pathInfo as $node) {
            $tripID = $node['trip_id'];
            insertUserTrip($userID, $tripID, $conn);


        }
        echo "<script>alert('Trips Added Successfully!')</script>";

        header("Location: mytrips.php");

    }
    echo $departure_station;
    echo $arrival_station;
    // Close the database connection
// }
    ?>





    <!DOCTYPE html>
    <html>
    <?php include 'navbar.php' ?>

    <head>
        <style>
            body {
                background-image: url('footerBg.jpg');
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center center;
                height: 100vh;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 380px;
                margin: 100px auto;
                padding: 20px;
                background-color: rgba(255, 255, 255, 0.8);
                border-radius: 10px;
            }

            .container h2 {
                text-align: center;
            }

            .container form {
                margin-top: 20px;
            }

            .container label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .container input[type="text"] {
                width: calc(100% - 20px);
                padding: 8px;
                margin-bottom: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .container input[type="submit"] {
                background-color: #4CAF50;
                color: white;
                cursor: pointer;
            }

            .container input[type="submit"]:hover {
                background-color: #45a049;
            }

            #navBar {

                position: relative;

            }
        </style>
    </head>

    <body>

        <div class="container">
            <?php

            // Print the path
            echo "<h3>The Stations From " . getStationName($departure_station, $conn) . " to " . getStationName($arrival_station, $conn) . ": </h3>";
            foreach ($pathInfo as $node) {
                echo "<h4>" . $node['station_name'] . " (Departure Time: " . $node['departure_time'] . ")" . "</h4>";
            }
            // .    
        
            echo "<h4>" . getStationName($destinationId, $conn) . " (Departure Time: " . getArrivalTime($arrival_station, $conn) . ") </h4> </br>";
            echo "<h3> Do you want to take this trip? </h3>";

            echo "</br><form action='" . $_SERVER['PHP_SELF'] . "' method='post' ><center><button type='submit' >Book trip</button></center><input type='hidden' name='trip_id'><input type='hidden' name='departure_station' value='" . $departure_station . "'><input type='hidden' name='arrival_station' value='" . $arrival_station . "'></form>";
    // print_r($pathInfo);
} else {
    echo "No path found from " . getStationName($departure_station, $conn) . " to " . getStationName($arrival_station, $conn);
}


?>

    </div>
</body>

</html>