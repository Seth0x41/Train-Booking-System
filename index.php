<?php
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require_once 'connectDB.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if form fields are set and not empty
    if (
        isset($_POST['userName']) && isset($_POST['userEmail']) && isset($_POST['userPassword'])
        && !empty($_POST['userName']) && !empty($_POST['userEmail']) && !empty($_POST['userPassword'])
    ) {

        // Retrieve form data
        $name = $_POST['userName'];
        $email = $_POST['userEmail'];
        $password = $_POST['userPassword'];

        // Check if the user already exists
        $checkQuery = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('User with this email already exists.');</script>";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare and execute the SQL statement
            $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $name, $email, $hashedPassword);
            if ($stmt->execute()) {
                echo "<script>alert('Signup successful!');</script>";
                header("Location: http://localhost/meso/");
                exit;
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if form fields are set and not empty
    if (
        isset($_POST['loginEmail']) && isset($_POST['loginPassword'])
        && !empty($_POST['loginEmail']) && !empty($_POST['loginPassword'])
    ) {
        // Retrieve form data
        $email = $_POST['loginEmail'];
        $password = $_POST['loginPassword'];

        // Prepare and execute the SQL statement
        $checkQuery = "SELECT id, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkQuery);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    // Login successful
                    $_SESSION['user'] = $row['id'];
                    echo "<script>alert('Login successful!');</script>";
                    header("Location: http://localhost/meso/");
                    exit;
                } else {
                    // Invalid password
                    echo "<script>alert('Invalid password.');</script>";
                }
            } else {
                // User does not exist
                echo "<script>alert('User does not exist.');</script>";
            }
        } else {
            // Error in prepared statement
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

?>


<!DOCTYPE html>
<html>


<body>
    <!--navigation Bar-->
    <?php include 'navbar.php' ?>
    <!--the page header section-->
    <div id="Header">
        <div id="headerLDiv">
            <img src="ENR_logo.png" height="150px">
            <H1>EGY-Electric train <span>railway</H1>
            <p>
                <span id="span1">booking your seat in the new Egyptian electric train railway became so</span><span
                    id="span2"> easy through our website.
                    just choose your destination and traveling</span><span id="span3"> date and book your seat.</span>
            </p>
        </div>
        <div id="headerRDiv">
            <?php
            // Check if user is logged in
            if (isset($_SESSION['user'])) {
                // User is logged in, show the form
                $query = "SELECT * FROM stations";
                $result = $conn->query($query);

                // Prepare the HTML options
                $options = '';

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Generate an option for each row
                        $options .= '<option value="' . $row['station_id'] . '">' . $row['station_name'] . '</option>';
                    }
                }

                ?>
                <form name="getcities" action="trips.php" method="post">
                    <h3 style="color:#03fc03;text-shadow: 5px 5px 5px #000;">Departure Station</h3>

                    <select name="departure_station" id="departure_station">
                        <?php echo $options; ?>
                    </select>
                    <h3 style="color:#03fc03;text-shadow: 5px 5px 5px #000;">Arrival Station</h3>
                    <select name="arrival_station" id="arrival_station">
                        <?php echo $options; ?>
                    </select>
                    </br>
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <?php
            }
            ?>
        </div>
    </div>
    <!--about us section-->
    <div id="aboutUs">
        <img src="map.png">
        <div>
            <h2>What is the new Egyptian fast train ?!</h2>
            <p>it's a high speed rail links with three lines and 2000 kilometers the first line is from
                Marsa Matrouh to ain sukhna, and the second line is from sixth of October and Abu Simple and the
                third and the last line connects the cities of Qena , Hurghada and Safaga.
                this project was under construction by Siemens Companies, Arab Constractors and Orascom Construction.
            </p>
        </div>
    </div>
    <!--contact us page section-->
    <div id="contactUs">
        <h2>C o n t a c t - u s.</h2>
        <p>if you are facing any problem or have a feedback to give,please don't hold back and contact us.</p>
        <form>
            <textarea></textarea><br>
            <button type="submit"><i class="fa-regular fa-paper-plane"></i></button>
            <button type="reset"><i class="fa-solid fa-rotate-right"></i></button>
        </form>
    </div>
    <!--footer section-->
    <div id="footer">
        <div>
            <div id="footerS1">
                <h3>O u r - T e a m <i class="fa-solid fa-people-group"></i></h3>
                <div id="ourTeamD">
                    <div class="teamDiv">
                        <img src="placeholder-image.png" height="150" width="150">
                        <div class="hiddenDiv">Dr/Rasha abo bakr</div>
                    </div>
                    <div class="teamDiv">
                        <img src="mysara.jpg" height="150" width="150">
                        <div class="hiddenDiv">Mysara E.Rabie</div>
                    </div>
                    <div class="teamDiv">
                        <img src="placeholder-image.png" height="150" width="150">
                        <div class="hiddenDiv">Ereny Azmy</div>
                    </div>
                    <div class="teamDiv">
                        <img src="placeholder-image.png" height="150" width="150">
                        <div class="hiddenDiv">Ali Rabie</div>
                    </div>
                    <div class="teamDiv">
                        <img src="placeholder-image.png" height="150" width="150">
                        <div class="hiddenDiv">Ahmed Mohamed</div>
                    </div>
                    <div class="teamDiv">
                        <img src="placeholder-image.png" height="150" width="150">
                        <div class="hiddenDiv">Mahmoud Ahmed</div>
                    </div>
                </div>
            </div>
        </div>
        <p id="footerS2">
            this website is made by computer science and chemistry department,faculty of science,zagazig university,
            under supervision of Dr/Rasha Abo Bakr, 2023 all cobyrights are reclaimed <i
                class="fa-regular fa-copyright"></i>
        </p>
    </div>
    <!--sign in - sign up section-->
    <div id="logContainer">
        <div id="logDiv">
            <ul>
                <li onclick="logFunc2()" id="logClose"><i class="fa-solid fa-xmark"></i></li>
                <li onclick="func1()">Sign Up</li>
                <li onclick="func2()">Login</li>
            </ul>
            <div id="logIn">
                <form name="login" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="email" placeholder="E-mail" class="logInInputs" name="loginEmail">
                    <input type="password" placeholder="password" class="logInInputs" name="loginPassword"><br>
                    <button type="submit"><i class="fa-solid fa-right-to-bracket"></i></button>
                </form>
            </div>
            <div id="signUp">
                <form name="signUp" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="text" placeholder="name" class="SignUpInputs" name="userName">
                    <input type="email" placeholder="E-Mail" class="SignUpInputs" name="userEmail">
                    <input type="password" placeholder="password" class="SignUpInputs" name="userPassword">
                    <input type="submit" name="submit">
                </form>
            </div>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>

</html>
<?php $conn->close();
?>