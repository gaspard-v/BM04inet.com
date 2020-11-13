<?php
class Session
{
    private $m_db;
    public function __construct($db)
    {
        $this->m_db = $db;
    }
    public function setCookie($user, $password, $skip = false, $name = "id", $value = false, $expires = false, $path = null, $domain = null, $secure = false, $httponly = true)
    {
        if ($path == null)  {
            require(dirname( dirname(__FILE__) ) . "/path.php");
            $path = $site;
        }
        $is_ok = true;
        if (!$skip) {
            $is_ok = $this->m_db->loginUser($user, $password);
        }
        if ($is_ok) {
            if (!$value) $value = bin2hex(openssl_random_pseudo_bytes(128));
            if (!$expires) $expires = time() + 365 * 24 * 3600;
            $cookieData = (object) array("id" => $value, "expires" => $expires);
            try {
                $this->m_db->setCookie($user, $value, date("Y-m-d H:i:s", $expires));
                setcookie($name, json_encode($cookieData), $expires, $path, $domain, $secure, $httponly);
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            return false;
        }
        return true;
    }
    public function loginUserCookie()
    {
        if (isset($_COOKIE["id"])) {
            try {
                $json = json_decode($_COOKIE["id"]);
                if (!isset($json->id)) return false;
                if (!isset($json->expires)) return false;
                $value = $json->id;
                $expires = $json->expires;
                $result = $this->m_db->getCookie($value);
            } catch (Exception $exception) {
                throw $exception;
            }
            if ($result) {
                if ($result->num_rows > 1) throw new Exception("Too many 'id' cookies");
                while ($row = $result->fetch_assoc()) {
                    try {
                        if (strtotime($row["expiration"]) != $expires) {
                            return false;
                        } else if (strtotime($row["expiration"]) < time()) {
                            return false;
                        } else return $row;
                    } catch (Exception $exception) {
                        throw $exception;
                    }
                }
            } else return false;
        } else return false;
    }
    static public function deleteCookie($name, $value = null, $path = null, $domain = null, $secure = false, $httponly = true)
    {
        if ($path == null)  {
            require(dirname( dirname(__FILE__) ) . "/path.php");
            $path = $site;
        }
        setcookie($name, $value, time() - 3600, $path, $domain, $secure, $httponly);
    }
    public function deleteSessionCookie($number = false)
    {
        try {
            if (isset($_COOKIE["id"]) && (!$number || $number === "all")) {
                $json = json_decode($_COOKIE["id"]);
                if (isset($json->id)) {
                    $methode = "id";
                    if($number) $methode = "user+id";
                    $this->m_db->deleteCookie($methode, $json->id);
                    $this->deleteCookie("id");
                } else throw new Exception("'id' element of cookie 'id' does not exist");
            } else throw new Exception("cookie 'id' not set");
        } catch (Exception $execption) {
            throw $execption;
        }
    }
}
