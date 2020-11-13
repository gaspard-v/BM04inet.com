<?php
//web_test : S80dd9xRBJBuc1Uy
class mariadb
{
    private $m_host;
    private $m_user;
    private $m_password;
    private $m_database;
    private $m_port;
    private $m_mysqli;

    public function __construct($host, $user, $password, $database, $port = 3306)
    {
        $this->m_host = $host;
        $this->m_user = $user;
        $this->m_password = $password;
        $this->m_database = $database;
        $this->m_port = $port;
    }
    public function BDDconnecte()
    {
        $this->m_mysqli = new mysqli($this->m_host, $this->m_user, $this->m_password, $this->m_database, $this->m_port);
        if ($this->m_mysqli->connect_errno) {
            throw new Exception("Ereur : Echec lors de la connexion à MySQL : (" . $this->m_mysqli->connect_errno . ") " . $this->m_mysqli->connect_error);
        } else {
            return $this->m_mysqli->host_info;
        }
    }

    public function registerUser($user, $password)
    //clear password, the function will hash the password !
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
            // $retour = "Le mot de passe ne convient pas ! le mot de passe doit faut au moins 8 caractère avec au moins une majusclue et minuscule et un nombre";
            throw new Exception("Bad Password");
        }
        $goodUsername = preg_match('/^[\w.-]*$/', $user);
        if (!$goodUsername || strlen($user) < 4 || strlen($user) >= 20) {
            throw new Exception("Bad Username");
        }
        $secure_username = mysqli_escape_string($this->m_mysqli, $user);
        $sql = "SELECT DISTINCT username FROM users WHERE username = '$secure_username'";
        if (!$result = $this->m_mysqli->query($sql)) {
            throw new Exception("Erreur : Echec de la requête :\n
            Query: " . $sql . "\n
            Errno: " . $this->m_mysqli->errno . "\n
            Error: " . $this->m_mysqli->error . "\n");
        }
        if ($result->num_rows !== 0) {
            throw new Exception("User already present in the database");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password)
            VALUES
            ('" . $secure_username . "', '$hash')";
        if (!$result = $this->m_mysqli->query($sql)) {
            throw new Exception("Erreur : Echec de la requête :\n
                Query: " . $sql . "\n
                Errno: " . $this->m_mysqli->errno . "\n
                Error: " . $this->m_mysqli->error . "\n");
        }
        return true;
    }

    public function loginUser($user, $password)
    //clear password, the function will hash the password !
    {
        $retour = false;
        $goodUsername = preg_match('/^[\w.-]*$/', $user);
        if (!$goodUsername || strlen($user) < 4 || strlen($user) >= 20) {
            throw new Exception("Bad Username");
        }
        $secure_username = mysqli_escape_string($this->m_mysqli, $user);
        $sql = "SELECT password FROM users WHERE username = '$secure_username'";
        if (!$result = $this->m_mysqli->query($sql)) {
            throw new Exception("Erreur : Echec de la requête :\n
            Query: " . $sql . "\n
            Errno: " . $this->m_mysqli->errno . "\n
            Error: " . $this->m_mysqli->error . "\n");
        } else if ($result->num_rows == 1) {
            while ($row = $result->fetch_assoc()) {
                $retour = password_verify($password, $row["password"]);
            }
        }
        return $retour;
    }
    public function setCookie($user, $cookie, $expiration = null, $data = null, $extra = null)
    {
        $secure_username = mysqli_escape_string($this->m_mysqli, $user);
        $sql = "SELECT DISTINCT rowid FROM users WHERE username = '$secure_username'";
        if (!$result = $this->m_mysqli->query($sql)) {
            throw new Exception("Erreur : Echec de la requête :\n
            Query: " . $sql . "\n
            Errno: " . $this->m_mysqli->errno . "\n
            Error: " . $this->m_mysqli->error . "\n");
        }
        if ($result->num_rows !== 1) {
            throw new Exception("Unknown User");
        }

        while ($row = $result->fetch_assoc()) {
            $sql = "INSERT INTO php_cookies (user_id, id_session";
            if ($data) $sql .= ", data";
            if ($expiration) $sql .= ", expiration";
            if ($extra) $sql .= ", extra";
            $sql .= ") VALUES (";
            $sql .= $row["rowid"];
            $sql .= ", '" . $cookie . "'";
            if ($data) $sql .= ", '" . $data . "'";
            if ($expiration) $sql .= ", '" . $expiration . "'";
            if ($extra) $sql .= ", '" . $extra . "'";
            $sql .= ")";
        }
        if (!$result = $this->m_mysqli->query($sql)) {
            throw new Exception("Erreur : Echec de la requête :\n
            Query: " . $sql . "\n
            Errno: " . $this->m_mysqli->errno . "\n
            Error: " . $this->m_mysqli->error . "\n");
        }
        return $result;
    }
    public function getCookie($cookie, $only_authentication = false)
    {
        $secure_cookie =  mysqli_escape_string($this->m_mysqli, $cookie);
        $sql = "SELECT DISTINCT * FROM php_cookies INNER JOIN users ON php_cookies.user_id = users.rowid WHERE id_session = '$secure_cookie'";
        if (!$result = $this->m_mysqli->query($sql)) {
            throw new Exception("Erreur : Echec de la requête :\n
            Query: " . $sql . "\n
            Errno: " . $this->m_mysqli->errno . "\n
            Error: " . $this->m_mysqli->error . "\n");
        }
        if($result->num_rows == 0)
        {
            return false;
        }
        if($only_authentication)
        {
            return true;
        }
        return $result;
    }

    public function deleteCookie($methode, $value)
    {
        $secure_value =  mysqli_escape_string($this->m_mysqli, $value);
        if($methode === "id")
        {
            $sql = "DELETE FROM php_cookies WHERE id_session = '$secure_value'";
            if (!$this->m_mysqli->query($sql)) {
                throw new Exception("Erreur : Echec de la requête :\n
                Query: " . $sql . "\n
                Errno: " . $this->m_mysqli->errno . "\n
                Error: " . $this->m_mysqli->error . "\n");
            }
        }
        else if($methode === "user")
        {
            $sql = "DELETE FROM php_cookies WHERE user_id = '$secure_value'";
            if (!$this->m_mysqli->query($sql)) {
                throw new Exception("Erreur : Echec de la requête :\n
                Query: " . $sql . "\n
                Errno: " . $this->m_mysqli->errno . "\n
                Error: " . $this->m_mysqli->error . "\n");
            }
        }
        else if($methode === "user+id")
        {
            $subQuery = "SELECT DISTINCT user_id FROM php_cookies WHERE id_session = '$secure_value'";
            $sql = "DELETE FROM php_cookies WHERE user_id = ($subQuery)";
            if (!$this->m_mysqli->query($sql)) {
                throw new Exception("Erreur : Echec de la requête :\n
                Query: " . $sql . "\n
                Errno: " . $this->m_mysqli->errno . "\n
                Error: " . $this->m_mysqli->error . "\n");
            }
        }
        else throw new Exception("invalide parameters :\n
        methode : $methode\n
        value : $value");
    }
}