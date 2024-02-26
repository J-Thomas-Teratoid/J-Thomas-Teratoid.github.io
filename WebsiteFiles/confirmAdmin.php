<?php
    session_start();

    include ("app.php");

    use App as App;

    $usernameError = "";

    //Set varaible as session varaible
    $tempUserID = $_SESSION["tempUserID"];

    $_SESSION["notConfirmed"] = 1;

    if (isset($_POST["confirm"]))
    {
        $usernameInput = $_POST["username"];

        if (strlen($usernameInput < 1))
        {
            $usernameError = "Nothing has been enter in field";
        }
        else 
        {
            $config = new Config();

            $stmt = $config->pdo->prepare("SELECT * FROM users WHERE ID = $tempUserID");

            $stmt->execute();

            while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $adminUsername = $result["Username"];

                if ($adminUsername == $usernameInput)
                {
                    $_SESSION["userIsAdmin"] = 1;
                    $_SESSION["notConfirmed"] = 0;
                    $_SESSION["username"] = $adminUsername;
                    $_SESSION["userID"] = $tempUserID;
                    header("location:adminHome.php");
                }
                else
                {
                    $usernameError = "Incorrect Username";
                }
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
    <title>Login</title>
<body>
    <form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method = "POST">
        <h2>To confirm you are the user of this account, please enter the account username</h2>
        <div class="mb-2">
            <label for = "username">Username</label>
            <input type = "text" name = "username" id = "username"></input>
            <label for = "username"><?php echo $usernameError; ?></label>
        </div>
        <input type = "submit" name = "confirm" value = "Confirm">
    </form>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</html>