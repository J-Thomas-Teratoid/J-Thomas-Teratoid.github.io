<?php
    // included files
    include ("Config\config.php");

    // included classes 
    use Config as Config;

    class App 
    {
        static public function addUser($userFirstName, $userLastName, $userEmail, $userPassword)
        {
            // creates connection to the database
            $config = new Config();

            $userPassword = password_hash($userPassword, PASSWORD_DEFAULT);

            //prepare an sql statement to be run 
            $stmt = $config->pdo->prepare("INSERT INTO users (FirstName, LastName, Email, HashedUserPassword) VALUES ('$userFirstName', '$userLastName', '$userEmail', '$userPassword')");
            // runs the statement
            $stmt->execute();

            $userID = $config->pdo->lastInsertId();

            $stmt2 = $config->pdo->prepare("INSERT INTO healthtracker (UserID) VALUES ($userID)");

            $stmt2->execute();
        }

        static public function editUser($userFirstName, $userLastName, $userEmail, $id)
        {
            // creates connection to the database
            $config = new Config();

            //prepare an sql statement to be run 
            $stmt = $config->pdo->prepare("UPDATE users SET FirstName = '$userFirstName', LastName = '$userLastName', Email = '$userEmail' WHERE ID = $id");
            // runs the statement
            $stmt->execute();
        }

        static public function addAdmin($userFirstName, $userLastName, $userEmail, $userPassword, $username, $title)
        {
            // creates connection to the database
            $config = new Config();

            $userPassword = password_hash($userPassword, PASSWORD_DEFAULT);

            //prepares an sql statement to be executed 
            $stmt = $config->pdo->prepare("INSERT INTO users (FirstName, LastName, Email, HashedUserPassword, Username, Title) VALUES ('$userFirstName', '$userLastName', '$userEmail', '$userPassword', '$username', '$title')");;
            // runs the statement
            $stmt->execute();

            $userID = $config->pdo->lastInsertId();

            $stmt2 = $config->pdo->prepare("INSERT INTO healthtracker (UserID) VALUES ($userID)");

            $stmt2->execute();
        }

        static public function getCurrentWeather($location, $apiKey)
        {
            // disables error in function so API error does not show onscreen
            ini_set('display_errors', 0);
            // creates a URL that calls the weather data from openweathermap
            $search = "https://api.openweathermap.org/data/2.5/weather?q=". $location ."&appid=". $apiKey."&units=metric";

            $currentWeather = file_get_contents($search);
            $currentWeather = json_decode($currentWeather, true);

            // if the currernt weather data is not defined...
            if (is_null($currentWeather))
            {
                //...  return an error
                return "ERROR";
            }
            else
            {
                // else return the current weather data 
                return $currentWeather;
            }

        }
        static public function getForecast($location, $apiKey)
        {
             // disables error in function so API error does not show onscreen
            ini_set('display_errors', 0);

            $fullForecast = '<h2> Forecast </h2>';

            // creates a URL that calls the weather data from openweathermap
            $search = "https://api.openweathermap.org/data/2.5/forecast?q=". $location ."&appid=". $apiKey ."&units=metric";

            $forecast = file_get_contents($search);
            $forecast = json_decode($forecast, true);

            $prevDate = "";

            if (is_null($forecast))
            {
                return "ERROR";
            }
            else
            {
                foreach ($forecast["list"] as $key => $value )
                {
                    if (date("F j, Y", $value["dt"]) == $prevDate)
                    {
                        $forecast = "<div class = 'col-3'>
                            <div class = 'card collapse multi-collapse' id='". date("j", $value["dt"]) ."'>
                                <div class='card-body'>
                                    <h4>". date("g:i a", $value["dt"]) ."</h4>
                                    <img src = http://openweathermap.org/img/w/". $value["weather"][0]["icon"].".png>
                                    <p>". $value["weather"][0]["main"] ."<br> Temperature: ". $value["main"]["temp"] ." °C <br> Humidity; ". $value["main"]["humidity"] ."% <br> Pressure: ". $value["main"]["pressure"] ." hPa </p>
                                </div>
                            </div>
                        </div>";
                    }
                    else
                    {
                        $forecast = "</div>
                            </div>
                            <div>
                                <a class='link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover' style='font-size: 30px' data-bs-toggle='collapse' href='#" . date("j", $value["dt"]) ."' role='button' aria-expanded='false' 'aria-controls=". date("j", $value["dt"]) .">". date("F j Y", $value["dt"]) ."</a>
                                <br>
                                <div class = 'row g-2'>
                                    <div class = 'col-3'>
                                        <div class = 'card collapse multi-collapse' id='". date("j", $value["dt"]) ."'>
                                            <div class='card-body'>
                                                <h4>". date("g:i a", $value["dt"]) ."</h4>
                                                <img src = http://openweathermap.org/img/w/". $value["weather"][0]["icon"].".png>
                                                <p>". $value["weather"][0]["main"] ."<br> Temperature: ". $value["main"]["temp"] ." °C <br> Humidity; ". $value["main"]["humidity"] ."% <br> Pressure: ". $value["main"]["pressure"] ." hPa </p>
                                            </div>
                                        </div>
                                    </div>";
                    }

                    $prevDate = date("F j, Y", $value["dt"]);
                    $fullForecast .= $forecast;
                }

                return $fullForecast;
            }
        }

        static public function getCoordinates($location, $apiKey)
        {
            ini_set('display_errors', 0);

            $searchCoord = "https://api.openweathermap.org/geo/1.0/direct?q=". $location ."&limit=1&appid=". $apiKey;
            $coord = file_get_contents($searchCoord);
            $coord = json_decode($coord, true);
            if (is_null($coord))
            {
                return "ERROR";
            }
            else
            {
                return $coord;
            }
        }

        static public function getAirQuality($lat, $lon, $apiKey)
        {
            ini_set('display_errors', 0);

            $search = "https://api.openweathermap.org/data/2.5/air_pollution?lat=". $lat ."&lon=". $lon . "&appid=". $apiKey;
            $airQuality= file_get_contents($search);
            $airQuality = json_decode($airQuality, true);
            if (is_null($airQuality))
            {
                return "ERROR";
            }
            else
            {
                return $airQuality;
            }
        }

        static public function getArticle($id)
        {
            // creates a connection to the SQL sever
            $config = new Config();

            $stmt = $config->pdo->prepare("SELECT * FROM articles WHERE ID = $id");

            $stmt->execute();

            while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $title = $result["Title"];
                $body = $result["Body"];
            }

            $article = "<div>
                <h1>" .$title. "</h1>
                <p>" .$body. "</p>
            </div>";

            return $article;
        }

        static public function getArticleLink($id)
        {
            
            // creates a connection to the SQL sever
            $config = new Config();

            $stmt = $config->pdo->prepare("SELECT * FROM articles WHERE ID = $id");

            $stmt->execute();

            while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $title = $result["Title"];
                $body = $result["Body"];
            }

            $link = "<a href='article.php?articleID=". $id ."'>". $title ."</a>";

            return $link;
        }

        static public function getArticleName($id)
        {
            // creates a connection to the SQL sever
            $config = new Config();

            $stmt = $config->pdo->prepare("SELECT * FROM articles WHERE ID = $id");

            $stmt->execute();

            while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $title = $result["Title"];
            }

            return $title;
        }

        static public function addArticle($title, $body)
        {
            $config = new Config();

            $stmt = $config->pdo->prepare("INSERT INTO articles (Title, Body) VALUES ('$title', '$body')");

            $stmt->execute();
        }

        static public function editArticle($title, $body, $id)
        {
            $config = new Config();

            $stmt = $config->pdo->prepare("UPDATE articles SET Title = '$title', Body = '$body' WHERE ID= $id");

            $stmt->execute();
        }

        static public function removeArticle($id) 
        {
            {
                // creates a connection to the SQL sever
                $config = new Config();
    
                $stmt = $config->pdo->prepare("DELETE FROM articles WHERE ID = $id");
    
                $stmt->execute();
            }
        }

        static public function editHealthData($weight, $height, $sleepTime, $id)
        {
            $config = new Config();

            $stmt = $config->pdo->prepare("UPDATE healthtracker SET UserWeight = $weight , UserTimeSleeping = $sleepTime, UserHeight = $height WHERE UserID = $id");

            $stmt->execute();
        }
    }
 
?>