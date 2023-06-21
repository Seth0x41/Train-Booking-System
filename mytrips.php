<?php
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require_once('connectDb.php');

session_start();
include 'navbar.php';
?>
<style>
    /* Global Styling */
    body {
        background-image: url('footerBg.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
        height: 100vh;
        margin: 0;
        padding: 0;
    }


    * {
        margin: 0;
        padding: 0;
        font-family: Arial;
        font-size: 14px;
        color: #eff0f2;
    }

    h1 {
        font-size: 30px;

    }

    /* Container */
    #container {
        width: 100%;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }


    /* Table */
    table {
        background: linear-gradient(180deg, rgb(24, 66, 109), rgb(25, 190, 198));
        width: 50%;
        height: 70%;
        border-collapse: collapse;
        box-shadow: 5px 5px 20px #38393a;
        cursor: pointer;
        border-radius: 25px;
        overflow: hidden;
        transition: all 0.5s;
    }

    th {
        text-transform: uppercase;
        letter-spacing: 1px;
        height: 30px;
    }

    td {
        text-align: center;
        text-transform: uppercase;
        font-size: 12px;
    }

    #header {
        background: rgb(24, 95, 175);
        height: 40px;
    }

    /* Hover Effects */
    th:hover {
        background: rgb(14, 53, 116);
    }

    tr:hover {
        background: rgba(206, 223, 239, 0.2);
    }

    td:hover,
    .planets:hover {
        background: rgb(255, 115, 0);
        font-size: 18px;
        font-weight: bold;
    }

    table:hover {
        width: 51%;
        transform: translate(0px, -10px);
        box-shadow: 15px 15px 66px #38393a;
    }
</style>

<?php
// Assuming you have established a database connection


$userID = $_SESSION['user'];
// Fetch the user ID from the session
$userId = $_SESSION['user'];

// SQL query to fetch the trips for the user
$query = "SELECT t.trip_id, s1.station_name AS departure_station, s2.station_name AS arrival_station
          FROM user_trip AS ut
          INNER JOIN trips AS t ON ut.trip_id = t.trip_id
          INNER JOIN stations AS s1 ON t.departure_station_id = s1.station_id
          INNER JOIN stations AS s2 ON t.arrival_station_id = s2.station_id
          WHERE ut.user_id = $userId";

// Execute the query
$result = mysqli_query($conn, $query);
// Check if any trips are found
if (mysqli_num_rows($result) > 0) {
    ?>
    <div id="container">
        <table>
            <thead>
                <tr>
                    <th>From</th>
                    <th>To</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Iterate through each row of the result set
                while ($row = mysqli_fetch_assoc($result)) {
                    $tripId = $row['trip_id'];
                    $departureStation = $row['departure_station'];
                    $arrivalStation = $row['arrival_station'];
                    ?>
                    <tr>
                        <td>
                            <?php echo $departureStation; ?>
                        </td>
                        <td>
                            <?php echo $arrivalStation; ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
} else {
    echo "<div id='container'>";
    echo "<h1 style:'font-size:30px;'>There is no Trips.</h1>";
    echo "</div>";
}

// Close the database connection
mysqli_close($conn);
?>