<?php
    class Config
    {
        // variables used for creating the DSN
        protected $host = "localhost";
        protected $username = "root";
        protected $password = "";
        protected $dbname = "digitalsolutionhag";

        // variable that will be used to connect to the database
        public $pdo;

        public function __construct()
        {
            // attempts to run the code 
            try 
            {
                // The dsn is created, using the variables defined in the class
                $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=UTF8";

                // connects to the mysql database using the DSN
                $this->pdo = new PDO($dsn, $this->username, $this->password);

            // validates errors if the database connection fails 
            } catch (PDOException $ex) {
                // echos the error message that occurs within the PDO connection 
                echo $ex->getMessage();
            }
        }

        public function getHost()
        {
            return $this->host;
        }

        public function getUsername()
        {
            return $this->username;
        }

        public function getPassword()
        {
            return $this->password;
        }

        public function getDbname()
        {
            return $this->dbname;
        }

    }
?>