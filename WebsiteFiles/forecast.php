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


    // variable for the openweathermap api key 
    $apiKey = "b472922fd5c9208ab43b85306300643a";
    $errormessage = "";
    $forecastToday = "";
    $LocationTitle = "";
    $fullForecast = "";
    $weatherAdvice = "";

    // checks if search button has been pressed 
    if (isset($_POST["searchLocationButton"]))
    {
        
        if (strlen($_POST["searchLocationInput"]) == 0)
        {
            echo "<script>alert('Please enter a location in the search box')</script>";
        }
        else
        {
            // gets input from search box 
            $location = $_POST["searchLocationInput"];
            $currentWeather = App::getCurrentWeather($location, $apiKey);
            if ($currentWeather == "ERROR")
            {
                echo "<script>alert('Invalid Location, please try again')</script>";
            }
            else
            {
                $airPollutionAPI = "http://api.openweathermap.org/data/2.5/air_pollution?lat=". $currentWeather["coord"]["lat"] ."&lon=". $currentWeather["coord"]["lon"] ."&appid=". $apiKey;
                $airPollution = file_get_contents($airPollutionAPI);
                $airPollution = json_decode($airPollution, true);

                if ($airPollution["list"][0]["main"]["aqi"] == 1)
                {
                    $airQuality = "Good";
                }
                else if ($airPollution["list"][0]["main"]["aqi"] == 2)
                {
                    $airQuality = "Fair";
                }
                else if ($airPollution["list"][0]["main"]["aqi"] == 3)
                {
                    $airQuality = "Moderate";
                }
                else if ($airPollution["list"][0]["main"]["aqi"] == 4)
                {
                    $airQuality = "Poor";
                }
                else if($airPollution["list"][0]["main"]["aqi"] == 5)
                {
                    $airQuality = "Very Poor";
                }
                else
                {
                    $airQuality = "ERROR";
                }
                $LocationTitle = "<h2>" . $currentWeather["name"] ."</h2>
                <br>";

                $forecastToday = "<div class = 'card w-100'>
                    <div class = 'card-body'>
                        <h3> Current Weather </h3>
                        <img src = http://openweathermap.org/img/w/". $currentWeather["weather"][0]["icon"].".png>
                        <p> " . $currentWeather["weather"][0]["main"] ." <br> Temperature: " . $currentWeather["main"]["temp"] ." °C <br> Humidity: " . $currentWeather["main"]["humidity"] ."% <br> Pressure: ".$currentWeather["main"]["pressure"]." hPa <br> Air Quality: ". $airQuality ."</p>
                    </div>
                </div>
                <br>";

                $weatherAdvice = "<h4> Here are some articles we reccomend to keep safe in today's weather</h4>
                <div class = 'row'>";

                if ($currentWeather["main"]["temp"] > 24)
                {
                    $weatherAdvice .= "<div>".App::getArticleLink(5) ."
                    </div>";
                }
                else if ($currentWeather["main"]["temp"] < 6)
                {
                    $weatherAdvice .= "<div>".App::getArticleLink(8) ."
                    </div>";
                }

                if ($airQuality == "Poor" or $airQuality == "Very Poor")
                {
                    $weatherAdvice .= "<div>".App::getArticleLink(9) ."
                    </div>";
                }

                if ($currentWeather["weather"][0]["main"] == "Snow")
                {
                    $weatherAdvice .= "<div>".App::getArticleLink(10) ."
                    </div>";
                }
                else if ($currentWeather["weather"][0]["main"] == "Rain")
                {
                    $weatherAdvice .= "<div>".App::getArticleLink(11) ."
                    </div>";
                }
                else if ($currentWeather["weather"][0]["main"] == "Fog")
                {
                    $weatherAdvice .= "<div>".App::getArticleLink(12) ."
                    </div>";
                }
                else if ($currentWeather["weather"][0]["main"] == "Thunderstorm")
                {
                    $weatherAdvice .= "<div>".App::getArticleLink(14) ."
                    </div>";
                }
                else if ($currentWeather["weather"][0]["main"] == "Tornado")
                {
                    $weatherAdvice .= "<div>".App::getArticleLink(15) ."
                    </div>";
                }

                if ($currentWeather["main"]["pressure"] > 1002 and $currentWeather["main"]["pressure"] < 1018)
                {
                    $weatherAdvice .= "<div>".App::getArticleLink(17) ."
                    </div>";
                }
                
                $weatherAdvice .= "<br>";
                $fullForecast = App::getForecast($location, $apiKey);
            }
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
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css">
    <link rel="stylesheet" href="Styles/styles.css">
    <title>Weather Forecast</title>
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
    <form class="d-flex flex-row pb-3" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method = "POST">
        <input type="text" name="searchLocationInput" id="searchLocationInput" placeholder="Search for a location"></input>
        <button class="btn btn-dark btn-sm" type="submit" name="searchLocationButton">🔍</button>
    </form>
    <?php
        echo $LocationTitle;
        echo $forecastToday;
        echo $weatherAdvice;
        echo $fullForecast;

        if (isset($airPollution))
        {
            echo "";
        }
        else
        {
            echo "<div id='map'></div>";
        }
    ?>
</body>
<script src="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js"></script>
<script src="Scripts/weathermap.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</html>