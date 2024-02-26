<?php
    session_start();

    include ("app.php");

    use App as App;

    // if sign out button is pressed...
    if (isset($_POST["sign out"]))
    {
        // Destroy all session variables
        session_destroy();
        // send user to home 
        header("location:index.php");
    }

    // if userID session variable is set...
    if (isset($_SESSION["userID"]))
    {
        //Set varaible as session varaible 
        $userID = $_SESSION["userID"];
    }

    // if session variable "not confirmed" is set to 1...
    if (isset($_SESSION["notConfirmed"]))
    {
        if ($_SESSION["notConfirmed"] == 1)
        {
            // unsets all session variables 
            session_destroy();
            header("location:login.php");
        }
    }

    // if userIsAdmin session variable is set...
    if (isset($_SESSION["userIsAdmin"]))
    {
        // set varable as session variable
        $userIsAdmin = $_SESSION["userIsAdmin"];
    }

    // if search button is pressed...
    if(isset($_POST["searchButton"]))
    {
        // put searchbox input into variable
        $search = $_POST["searchInput"];

        //presence check
        if (strlen($search) < 1)
        {
            // if empty, send alert box to user
            echo "<script>alert('Please enter an input in the search box')</script>";
        }
        else 
        {
            // else send user to search page
            header("location:searchArticles.php?searchInput=". $search);
        }
    }

    $weightError = "";
    $sleepTimeError = "";
    $heightError = "";
    $isInvalid = FALSE;

    $config = new Config;

    $stmt = $config->pdo->prepare("SELECT * FROM healthtracker WHERE userID = $userID");

    $stmt->execute();

    while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
    {
        $weight = $result["UserWeight"];
        $sleepTime = $result["UserTimeSleeping"];
        $height = $result["UserHeight"];
    }

    if (isset($_POST["save"]))
    {
        $weightInput = $_POST["weight"];
        $heightInput = $_POST["height"];
        $sleepTimeInput = $_POST["timeSlept"];

        if (is_numeric($weightInput) == FALSE)
        {
            $weightError = "Invalid input, must enter a number";
            $isInvalid = TRUE;
        }
        else if ($weightInput < 20 or $weightInput > 400)
        {
            $weightError = "Input must between 20 and 400 KG";
            $isInvalid = TRUE;
        }

        if (is_numeric($heightInput) == FALSE)
        {
            $heightError = "Invalid input, must enter a number";
            $isInvalid = TRUE;
        }
        else if ($heightInput < 0.6 or $heightInput > 2.72)
        {
            $heightError = "Input must between 0.6 and 2.72 meters";
            $isInvalid = TRUE;
        }


        if (is_numeric($sleepTimeInput) == FALSE)
        {
            $sleepTimeError = "Invalid input, must enter a number";
            $isInvalid = TRUE;
        }
        else if ($sleepTimeInput < 0 or $sleepTimeInput > 24)
        {
            $sleepTimeError = "Input must between 0 and 24 hours";
            $isInvalid = TRUE;
        }

        if ($isInvalid == FALSE)
        {
            $_SESSION["prevWeight"] = $weight;
            App::editHealthData($weightInput, $heightInput, $sleepTimeInput, $userID);
            header("location:myAccount.php");
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>Document</title>
    <div class="d-flex justify-content-end">
        <?php
            if (isset($userIsAdmin))
            {
                echo "<div style='width:85px;'>
                <a href = 'adminHome.php'>AdminPage</a>
            </div>
            <div style='width:85px;'>
                <a href = 'SignOut.php'>Sign Out</a>
            </div>";
            } else if (!isset($userID))
            {
                echo "<div style='width:65px;'>
                <a href = 'register.php'>Register</a>
            </div>
            <div style='width:50px;'>
                <a href = 'login.php'>Login</a>
            </div>";
            }
            else
            {
               echo "<div style='width:85px;'>
                    <a href = 'SignOut.php'>Sign Out</a>
                </div>";
            }
        ?>
    </div>
    <nav class="navbar navbar-expand-lg bg-body-tertiary navbar bg-dark " data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">LOGO</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="forecast.php">Forecast</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="airQuality.php">Air Quality</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="articlesPage.php">Articles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php if (isset($userID)){ echo "myAccount.php";}else{echo "login.php";} ?>">My Account</a>
                </li>
            </ul>
            <form class="d-flex" role="search" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method = "POST">
                <input class="form-control me-2" type="search" placeholder="Search for articles..." aria-label="Search" name = "searchInput">
                <button class="btn btn-outline-success" type="submit" name = "searchButton">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                </button>
            </form>
            </div>
        </div>
    </nav>
</head>
<body>
    <form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method = "POST">
        <div class="container border rounded w-auto p-3 position-absolute top-50 start-50 translate-middle">
            <div class="form-outline">
                <label for = "weight" class="form-label">Weight (KG)</label>
                <input type = "text" class="form-control" name = "weight" id = "weight" value = "<?php echo $weight; ?>"></input>
                <label for = "weight" class="form-label"><?php echo $weightError ?></label>
            </div>
            <div class="form-outline">
                <label for = "height" class="form-label">Height (Meters)</label>
                <input type = "text" class="form-control" name = "height" id = "height" value = "<?php echo $height; ?>"></input>
                <label for = "height" class="form-label"><?php echo $heightError ?></label>
            </div>
            <div class="form-outline">
                <label for = "timeSlept" class="form-label">Time Slept (Hours)</label>
                <input type = "text" class="form-control" name = "timeSlept" id = "timeSlept" value = "<?php echo $sleepTime; ?>"></input>
                <label for = "timeSlept" class="form-label"><?php echo $sleepTimeError ?></label>
            </div>
            <input type = "submit" class = "btn btn-outline-dark btn-sm" name = "save" value = "Save">
        </div>
    </form>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</html>