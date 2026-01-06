<?php

	if ($_SERVER['REQUEST_METHOD'] === 'GET' && basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
        http_response_code(403);
        include 'access_guard.php';
        exit;
    }

	require_once 'init.php'; 

	define("DB_HOST", "localhost");
	define("DB_USER", "root");
	define("DB_PASS", "");
	define("DB_NAME", "canteen1");

	function determineRedirectURL($user_type) {
	    $allowed = ['Administrator', 'Staff', 'Canteen Staff', 'Canteen Manager'];
	    if (!in_array($user_type, $allowed)) return 'index.php';

	    return match($user_type) {
	        'Administrator', 'Staff'      => 'Admin/index.php',
	        'Canteen Staff'              => 'Canteen Staff/index.php',
	        'Canteen Manager'            => 'Canteen Manager/index.php',
	        default                      => 'index.php',
	    };
	}

	function determineRedirectURLByLevel($userlevel_id) {
	    return match($userlevel_id) {
	        1 => 'Admin/index.php',            // Admin
	        2 => 'Canteen Staff/index.php',    // Canteen Staff
	        3 => 'Canteen Manager/index.php',  // Canteen Manager
	        default => 'index.php',
	    };
	}

	class db_connect {
	    private $host = DB_HOST;
	    private $user = DB_USER;
	    private $pass = DB_PASS;
	    private $name = DB_NAME;
	    protected $conn;
	    public $error;

	    public function connect() {
	        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->name);
	        mysqli_query($this->conn, "SET time_zone = '+08:00'");

	        if ($this->conn->connect_error) {
	            error_log("Database connection error: " . $this->conn->connect_error, 3, __DIR__ . '/logs/db_errors.log');
	            $this->error = "Fatal Error: Can't connect to database.";
	            return false;
	        }

	        return true;
	    }

	    public function __destruct() {
	        if ($this->conn) {
	            $this->conn->close();
	        }
	    }
	}
?>
