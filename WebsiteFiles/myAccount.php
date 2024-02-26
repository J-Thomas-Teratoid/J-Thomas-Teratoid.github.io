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

    if (isset($_SESSION["prevWeight"]))
    {
        $prevWeight = $_SESSION["prevWeight"];
    }

    $config = new Config;

    $stmt = $config->pdo->prepare("SELECT * FROM users WHERE ID = $userID");

    $stmt->execute();

    while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
    {
        $firstNameInput = $result["FirstName"];
        $lastNameInput = $result["LastName"];
        $emailInput = $result["Email"];
    }

    $stmt2 = $config->pdo->prepare("SELECT * FROM healthtracker WHERE UserID = $userID");
    
    $stmt2->execute();

    while ($result = $stmt2->fetch(PDO::FETCH_ASSOC))
    {
        $weight = $result["UserWeight"];
        $sleepTime = $result["UserTimeSleeping"];
        $height = $result["UserHeight"];
    }

    if ($height == 0 or is_null($weight))
    {
        $BMI = "Insufficient data, need both weight and height";
    }
    else
    {
        $BMI = $weight / ($height * $height);

        if ($BMI < 18.5)
        {
            $BMI = "Underweight";
        }
        else if ($BMI > 18.4 and $BMI < 25)
        {
            $BMI = "Normal";
        }
        else if ($BMI > 24.9 and $BMI < 40)
        {
            $BMI = "Overweight";
        }
        else if ($BMI > 39.9)
        {
            $BMI = "Obese";
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
    <title>My Account</title>
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
    <div class = "container-fluid">
        <div class = "row">
            <div class = "col-6">
            <h2 class = "text-center">Account Details</h2>
                <fieldset disabled>
                    <div class="form-outline">
                        <label for = "firstName" class="form-label">First Name</label>
                        <input type = "text" class="form-control" name = "firstName" id = "firstName" value = "<?php echo $firstNameInput ?>"></input>
                    </div>
                    <div class="form-outline">
                        <label for = "lastName" class="form-label">Last Name</label>
                        <input type = "text" class="form-control" name = "lastName" id = "lastName" value = "<?php echo $lastNameInput ?>"></input>
                    </div>
                    <div class="form-outline">
                        <label for = "email" class="form-label">Email</label>
                        <input type = "email" class="form-control" name = "email" id = "email" value = "<?php echo $emailInput ?>"></input>
                    </div>
                </fieldset>
                <a href = "editAccountDetails.php">Edit Account Details</a>
            </div>
            <div class = "col-6 justify-content-end">
                <h2 class = "text-center">Health Tracker</h2>
                <div class="mb-2 text-end">
                    <h4>Last Recorded Weight</h4>
                    <h3><?php if (is_null($weight)){echo "No recorded weight";}else{echo "$weight KG";} ?></h3>
                    <?php
                        if (isset($prevWeight))
                        {
                            $weightDifference = $weight - $prevWeight;
                            if ($weightDifference < 0)
                            {
                                echo "<p>$weightDifference compared to previously entered weight</p>
                                <br>";
                            }
                            else if ($weightDifference > 0)
                            {
                                echo "<p>+$weightDifference compared to previously entered weight</p>
                                <br>";
                            }
                            else if ($weightDifference == 0)
                            {
                                echo "<p>No difference compared to previously entered weight</p>
                                <br>";
                            }
                        }
                    ?>
                </div>
                <br>
                <div class="mb-2 text-end">
                    <h4>Last Recorded Time Slept</h4>
                    <h3><?php if (is_null($sleepTime)){echo "No recorded time";}else{echo "$sleepTime Hours";} ?></h3>
                </div>
                <br>
                <div class="mb-2 text-end">
                    <h4>BMI</h4>
                    <h3><?php echo $BMI; ?></h3>
                </div>
                <br>
                <div class = "d-grid gap-2 d-md-flex justify-content-md-end">
                    <a class = "justify-content-end" href = "editHealthDetails.php">Update Health Details</Details></a>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</html>