<?php
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
session_start();
require_once 'connectDB.php';

$user_role = 0;
// Check if the user is logged in and the session variable is set
if (isset($_SESSION['user'])) {
    // Retrieve the user ID from the session variable
    $userId = $_SESSION['user'];

    // Prepare the SQL query to check the role of the user
    $query = "SELECT role FROM users WHERE id = ?";

    // Prepare the statement
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Bind the user ID parameter and execute the statement
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();

        if ($result) {
            // Check if there is a row returned
            if ($result->num_rows > 0) {
                // Fetch the role value
                $row = $result->fetch_assoc();
                $role = $row['role'];

                // Check the role value and perform actions accordingly
                if ($role == 1) {
                    // Role is equal to 1
                    $user_role = 1;
                } else {
                    // Role is not equal to 1
                    $user_role = 0;
                }
            } else {
                // No rows returned
                echo "<h1>Something Wrong</h1>";
            }

            // Free the result set
            $result->free();
        } else {
            echo "Error executing the query: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
    }
} else {
    echo "<script>alert(User not logged in.)</script>";
}






if ($user_role != 1) {
    echo "<center><h1>You are not authorized to access this page</h1></center>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["trainNumber"])) {
        // Retrieve the train number from the form
        $trainNumber = $_POST["trainNumber"];

        // Check if the train number is not empty
        if (!empty($trainNumber)) {
            // Prepare the INSERT statement
            $query = "INSERT INTO trains (train_number) VALUES (?)";
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                // Error handling for the prepare statement
                echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
            } else {
                // Bind the parameter and execute the statement
                $stmt->bind_param("s", $trainNumber);
                $stmt->execute();

                // Check if the insertion was successful
                if ($stmt->affected_rows > 0) {
                    echo "<script>alert('Train Added!');</script>";
                } else {
                    echo "<script>alert('Error while adding train');</script>";
                }

                // Close the statement
                $stmt->close();
            }
        } else {
            echo "Please enter the train number.";
        }
    }

    if (isset($_POST["stationName"])) {
        // Retrieve the station name from the form
        $stationName = $_POST["stationName"];

        // Check if the station name is not empty
        if (!empty($stationName)) {
            // Prepare the INSERT statement
            $query = "INSERT INTO stations (station_name) VALUES (?)";
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                // Error handling for the prepare statement
                echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
            } else {
                // Bind the parameter and execute the statement
                $stmt->bind_param("s", $stationName);
                $stmt->execute();

                // Check if the insertion was successful
                if ($stmt->affected_rows > 0) {
                    echo "<script>alert('Station Added!');</script>";
                } else {
                    echo "<script>alert('Error while adding station');</script>";
                }

                // Close the statement
                $stmt->close();
            }
        } else {
            echo "Please enter the station name.";
        }
    }
}






// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["train_number"]) && isset($_POST["departure_station"]) && isset($_POST["arrival_station"]) && isset($_POST["departure_time"]) && isset($_POST["arrival_time"])) {
        // Retrieve the form data
        $train_number = $_POST["train_number"];
        $departure_station = $_POST["departure_station"];
        $arrival_station = $_POST["arrival_station"];
        $departure_time = $_POST["departure_time"];
        $arrival_time = $_POST["arrival_time"];

        // Prepare the INSERT statement
        $query = "INSERT INTO trips (train_id, departure_station_id, arrival_station_id,departure_time,arrival_time) VALUES (?, ?, ?,?,?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            // Error handling for the prepare statement
            echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        } else {
            // Bind the parameters and execute the statement
            $stmt->bind_param("sssss", $train_number, $departure_station, $arrival_station, $departure_time, $arrival_time);
            $stmt->execute();

            // Check if the insertion was successful
            if ($stmt->affected_rows > 0) {
                echo "<script>alert('Trip Added!');</script>";

            } else {
                echo "<script>alert('Error while adding Trip');</script>";
            }

            // Close the statement
            $stmt->close();
        }
    }
}
// Close the database connection
?>





<!DOCTYPE html>
<html>
<?php include 'navbar.php' ?>

<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
    <button id="addButton">Add Container</button>
    <button id="deleteButton">Delete Container</button>
    <div class="container">
        <div id="add_container">
            <h2>Add Train Data</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <label for="trainNumber">Train Number:</label>
                <input type="text" id="trainNumber" name="trainNumber" required>

                <input type="submit" value="Add Train">
            </form>

            <h2>Add Station Data</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <label for="stationName">Station Name:</label>
                <input type="text" id="stationName" name="stationName" required>

                <input type="submit" value="Add station">
            </form>

            <h2>Add Trips</h2>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <?php
                $query_trains = "SELECT trains.train_id, trains.train_number FROM trains";
                $query_stations = "SELECT stations.station_id, stations.station_name FROM stations";
                $result_trains = $conn->query($query_trains);
                // Prepare the HTML options
                $train_number = '';
                if ($result_trains->num_rows > 0) {
                    while ($row_train = $result_trains->fetch_assoc()) {
                        // Generate an option for each row
                        $train_number .= '<option value="' . $row_train['train_id'] . '">' . $row_train['train_number'] . '</option>';
                    }
                }
                $result_stations = $conn->query($query_stations);
                // Prepare the HTML options
                $station_name = '';
                if ($result_stations->num_rows > 0) {
                    while ($row_station = $result_stations->fetch_assoc()) {
                        // Generate an option for each row
                        $station_name .= '<option value="' . $row_station['station_id'] . '">' . $row_station['station_name'] . '</option>';
                    }
                }

                ?>
                <label>Train number</label>
                <select name="train_number" id="train_number">
                    <?php echo $train_number; ?>
                </select>
                <label>Departure Station</label>

                <select name="departure_station" id="departure_station">
                    <?php echo $station_name; ?>
                </select>
                <label>Arrival Station</label>
                <select name="arrival_station" id="arrival_station">
                    <?php echo $station_name; ?>
                </select>
                <input type="time" id="departure_time" name="departure_time">
                <input type="time" id="arrival_time" name="arrival_time">
                <input type="submit">
            </form>
        </div>

    </div>


</body>

</html>
<?php $conn->close();
?>