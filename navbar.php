<?php
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

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

?>

<head>
    <title>
        <?php echo getPageTitle(); ?>
    </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="fontawesome/css/all.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swaep" rel="stylesheet">
</head>

<?php
function getPageTitle()
{
    $currentPageURL = $_SERVER['PHP_SELF'];

    $pageName = basename($currentPageURL);

    $pageTitle = pathinfo($pageName, PATHINFO_FILENAME);

    $pageTitle = ucwords(str_replace('-', ' ', $pageTitle));

    return $pageTitle;
}
?>
<div id="navBar">
    <h3><a href='index.php' style="color: inherit; text-decoration: none;">EGY-Electric train <span>railway <i
                    class="fa-solid fa-train"></i></span></a></h3>
    <ul>
        <?php
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user'];
            $query = "SELECT name FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $username = $row['name'];
                echo '<li><a href="mytrips.php" style="color: inherit; text-decoration: none;">Welcome, ' . $username . '!</li>';
                echo '<a href="#contactUs" ><li><span>contact us <i class="fa-solid fa-envelope"></i></span></li></a>';
                echo '<a href="#aboutUs"><li><span>about us <i class="fa-solid fa-circle-info"></i></span></li></a>';
                if ($user_role == 1) {
                    echo '<a href="control.php"><li><span>Control Panal</span></li></a>';
                }
                echo '<li><a href="logout.php" style="color: inherit; text-decoration: none;">logout! <i class="fa-solid fa-arrow-right-to-bracket"></i></a></li>';
            }

        } else {
            echo '<li onclick="logFunc()">Login/Signup <i class="fa-solid fa-arrow-right-to-bracket"></i></li>';
            echo '<a href="#contactUs"><li><span>contact us <i class="fa-solid fa-envelope"></i></span></li></a>';
            echo '<a href="#aboutUs"><li><span>about us <i class="fa-solid fa-circle-info"></i></span></li></a>';

        }
        ?>
    </ul>
</div>