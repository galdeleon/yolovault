<?php
    include "credentials/redis";
    include "credentials/captcha";

    session_start();
    $DEBUG = isset($_GET["debug"]);

    if ($DEBUG) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    require "lib/predis/autoload.php";
    Predis\Autoloader::register();

    try {
        $redis = new Predis\Client(array("password" => $REDIS_PASS));
    }
    catch (Exception $e) {
        die($e->getMessage());
    }

    function download($file) {
        $file = preg_replace("/[.\/]+/", "", $file);
        $file .= ".php";
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: text/plain');
            header('Content-Disposition: inline; filename="'.basename($file).'"');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }

    function verify_captcha($response) {
        global $RECAPTCHA_SECRET;
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, 2);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, "secret=$RECAPTCHA_SECRET&response=$response");

        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if ($result["success"] === false) {
            die("Invalid captcha");
        }
    }

    function register_user($username, $password) {
        global $redis; 

        $user_id = $redis->hget("users", $username);
        if (!$user_id) {
            $user_id = $redis->incr("user_idx");
            $redis->hset("user:{$user_id}", "username", $username);
            $redis->hset("user:{$user_id}", "password", sha1($password));
            $redis->hset("user:{$user_id}", "isadmin", "ne");

            $redis->hset("users", $username, $user_id);
            return TRUE;
        }
        return FALSE;
    }

    function add_bot_link($link) {
        global $redis;

        $link_id = $redis->incr("link_idx");
        $redis->hset("link:{$link_id}", "id", $link_id);
        $redis->hset("link:{$link_id}", "link", $link);
        $redis->hset("link:{$link_id}", "submit-time", time());
        $redis->hset("link:{$link_id}", "visit-time", 0);
        $redis->hset("link:{$link_id}", "ip", $_SERVER["REMOTE_ADDR"]);

        $redis->rpush("links:unvisited", $link_id);

        return $link_id;
    }

    function get_bot_link_details($id) {
        global $redis;

        $visit_time = $redis->hget("link:{$id}", "visit-time");
        $submit_time = $redis->hget("link:{$id}", "submit-time");
        $ip = $redis->hget("link:{$id}", "ip");

        if ($ip != $_SERVER["REMOTE_ADDR"]) {
            die("Sorry, but looks like your IP differs from the one used for this submission. I can't give you info about other users submissions!");
        }

        if ($visit_time == 0) {
            $links = $redis->lrange("link:unvisited", 0, -1);
            $cntr = 0;
            foreach ($links as $link) {
                if ($link == $id)
                    break;
                $cntr++;
            }

            return array("visited" => false, "position" => $cntr);
        } else {
            $how_long = $visit_time - $submit_time;
            $when = time() - $visit_time;

            return array("visited" => true, "when" => $when, "how_long" => $how_long);
        }
    }

    function get_user($user_id) {
        global $redis;

        if (logged_in()) {
            $username = $redis->hget("user:{$user_id}", "username");
            $password = $redis->hget("user:{$user_id}", "password");
            $fname = $redis->hget("user:{$user_id}", "fname");
            $lname = $redis->hget("user:{$user_id}", "lname");
            $secret = $redis->hget("user:{$user_id}", "secret");
            $isadmin = $redis->hget("user:{$user_id}", "isadmin");

            return array("id" => $user_id,
                "username" => $username,
                "password" => $password,
                "fname" => $fname,
                "lname" => $lname,
                "secret" => $secret,
                "isadmin" => $isadmin);
        }
        return NULL;
    }


    function update_user($fname, $lname) {
        global $redis;

        if (logged_in()) {
            $redis->hset("user:{$_SESSION["id"]}", "fname", $fname);
            $redis->hset("user:{$_SESSION["id"]}", "lname", $lname);
        }
    }

    function update_secret($secret) {
        global $redis;

        if (logged_in()) {
            $redis->hset("user:{$_SESSION["id"]}", "secret", $secret);
        }
    }

    function verify_password($username, $password) {
        global $redis;

        $user_id = $redis->hget("users", $username);
        if ($user_id) {
            $real_pass = $redis->hget("user:{$user_id}", "password");
            if ($real_pass == sha1($password)) {
                return $user_id;
            }
        }
        return FALSE;
    }


    function debug($str) {
        global $DEBUG;
        if ($DEBUG) {
            echo "<p style='color:red'>{$str}</p>";
        }
    }

    function logged_in() {
        return $_SESSION["logged_in"];
    }

    function is_admin() {
        return $_SESSION["isadmin"] === "hellyeah";
    }

    function require_login() {
            if (!logged_in()) {
            header("Location: /?page=login");
            exit;
        }
    }

    function verify_csrf($token) {
        if (!logged_in() || $token !== $_SESSION["csrf"]) {
            die("surfing is not allowed sorry");
        }
    }

    function require_admin() {
            if (!is_admin()) {
                die("You no admiN");
        }
    }

    function load_user_details() {
        global $db;
        if (logged_in()) {
            $user = get_user($_SESSION["id"]);

            $_SESSION["username"] = $user["username"];
            $_SESSION["fname"] = $user["fname"];
            $_SESSION["lname"] = $user["lname"];
            $_SESSION["secret"] = $user["secret"];
            $_SESSION["isadmin"] = $user["isadmin"];
        }
    }

    if (!isset($_SESSION["logged_in"])) {
        $_SESSION["logged_in"] = false;
    } 
    load_user_details();