<html>
<head>
<title>Creating MySQL Tables</title>
</head>
<body>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("../core/includes/bootstrap.func.php");
	 
// SQL server creds
$servername = _DB_HOST;
$username = _DB_USER;
$password = _DB_PASS;
$dbname = _DB_NAME;
$dbadapter = _DB_ADAPTER;
$conx = new PDO("$dbadapter:host=$servername;dbname=$dbname", $username, $password);
$o_database = new db(); 

// Table creation state
$boUsers = false;
$boRoles = false;
$boUserroles = false;
$boMenu = false;

$timestamp = time();

function table_exist($table, $connection){
	$stmt = $connection->prepare('show tables like :table');
	$stmt->bindParam(':table', $table);
	$stmt->execute();
	return ($stmt->rowCount() > 0); 
}

echo "<br>This script will add tables and begining values, if those tables do not already exist.</br>";
echo "<br>Manually drop tables if you want this script to impact them.</br>";
echo "<br>Beginning database script...</br>";

if(!table_exist("users", $conx)){
	$newTable = true;		
}else{
	$newTable = false;
}
try {
    $conn = new PDO("$dbadapter:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$s_sql = "CREATE TABLE IF NOT EXISTS users( ".
		   "uid INT NOT NULL AUTO_INCREMENT, ".
		   "uname VARCHAR(20) NOT NULL, ".
		   "upass VARCHAR(60) NOT NULL, ".
		   "umail VARCHAR(75) NOT NULL, ".
		   "uphone VARCHAR(25), ".
		   "active TINYINT, ".
		   "lname VARCHAR(50), ".
		   "fname VARCHAR(30), ".
		   "mname VARCHAR(30), ".
		   "pname VARCHAR(30), ".
		   "create_date INT NOT NULL, ".
		   "change_date INT, ".
		   "change_uid INT, ".
		   "intid VARCHAR(300), ".		   
		   "reset_key VARCHAR(6), ".
		   "PRIMARY KEY ( uid )); ";
    // use exec() because no results are returned
    $conn->exec($s_sql);
    if($newTable){echo "<br>Table 'users' created successfully</br>";}else{echo "<br>Table 'users' not changed</br>";}
    }
catch(PDOException $e)
    {
    echo $s_sql . "<br>Error for users table:" . $e->getMessage();
    }
	
if(table_exist("users", $conx)){
	$boUsers = true;		
}

$conn = null;

// ADD ADMIN USER DEFAULT VALUES
if($boUsers && $newTable){
	//Set the query
	$o_database->query('INSERT INTO users (uname, upass, uphone, umail, active, pname, create_date, change_date, change_uid) VALUES (:uname, :upass, :phone, :umail, :active, :pname, :create_date, :change_date, :change_uid)');
	//Bind the data
	$o_database->bind(':uname', 'admin');
	$o_database->bind(':upass', password_hash("weakpassword", PASSWORD_BCRYPT));
	$o_database->bind(':phone', '');
	$o_database->bind(':umail', 'changeaddress@example.com');
	$o_database->bind(':active', '1');
	$o_database->bind(':pname', 'Administrator');
	$o_database->bind(':create_date', $timestamp);
	$o_database->bind(':change_date', $timestamp);
	$o_database->bind(':change_uid', '0');

	//Execute the query
	$o_database->execute();
}

if(!table_exist("roles", $conx)){
	$newTable = true;		
}else{
	$newTable = false;
}
try {
    $conn = new PDO("$dbadapter:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$s_sql = "CREATE TABLE IF NOT EXISTS roles( ".
		   "rid INT NOT NULL AUTO_INCREMENT, ".
		   "rname VARCHAR(20) NOT NULL, ".
		   "rdesc VARCHAR(75) NOT NULL, ".
		   "PRIMARY KEY ( rid ), ".
		   "UNIQUE ( rname ))";
    // use exec() because no results are returned
    $conn->exec($s_sql);
    if($newTable){echo "<br>Table 'roles' created successfully</br>";}else{echo "<br>Table 'roles' not changed</br>";}
    }
catch(PDOException $e)
    {
    echo $s_sql . "<br>Error for roles table:" . $e->getMessage();
    }
	
if(table_exist("roles", $conx)){
	$boRoles = true;		
}	

$conn = null;

// ADD ADMIN ROLE DEFAULT VALUES
if($boRoles && $newTable){
	//Set the query
	$o_database->query('INSERT INTO roles (rname, rdesc) VALUES (:rname, :rdesc)');
	//Bind the data
	$o_database->bind(':rname', 'administrator');
	$o_database->bind(':rdesc', 'Full site administration');
	//Execute the query
	$o_database->execute();
	//Bind the data
	$o_database->bind(':rname', 'superuser');
	$o_database->bind(':rdesc', 'Expanded privileges');
	//Execute the query
	$o_database->execute();
	//Bind the data
	$o_database->bind(':rname', 'user');
	$o_database->bind(':rdesc', 'Minimal privileges');
	//Execute the query
	$o_database->execute();
}

if(!table_exist("userroles", $conx)){
	$newTable = true;	
}else{
	$newTable = false;
}
try {
    $conn = new PDO("$dbadapter:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$s_sql = "CREATE TABLE IF NOT EXISTS userroles( ".
		   "id INT NOT NULL AUTO_INCREMENT, ".
		   "uid INT NOT NULL, ".
		   "rid INT NOT NULL, ".
		   "create_date INT NOT NULL, ".
		   "change_uid INT, ".		   
		   "PRIMARY KEY ( id ), ".
		   "FOREIGN KEY (uid) REFERENCES users(uid), ".
		   "FOREIGN KEY (rid) REFERENCES roles(rid)); ";
    // use exec() because no results are returned
    $conn->exec($s_sql);
    if($newTable){echo "<br>Table 'userroles' created successfully</br>";}else{echo "<br>Table 'userroles' not changed</br>";}
    }
catch(PDOException $e)
    {
    echo $s_sql . "<br>Error for userroles table:" . $e->getMessage();
    }
	
if(table_exist("userroles", $conx)){
	$boUserroles = true;
}		

$conn = null;

// ADD ADMIN USERROLE DEFAULT VALUES
if($boUserroles && $newTable){
	//Set the query
	$o_database->query('INSERT INTO userroles (uid, rid, create_date, change_uid) VALUES (:uid, :rid, :create_date, :change_uid)');
	//Bind the data
	$o_database->bind(':uid', '1');
	$o_database->bind(':rid', '1');
	$o_database->bind(':create_date', $timestamp);
	$o_database->bind(':change_uid', '0');
	//Execute the query
	$o_database->execute();
}

if(!table_exist("menu", $conx)){
	$newTable = true;		
}else{
	$newTable = false;
}
try {
    $conn = new PDO("$dbadapter:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$s_sql = "CREATE TABLE IF NOT EXISTS menu( ".
		   "mid INT NOT NULL AUTO_INCREMENT, ".
		   "mname VARCHAR(20) NOT NULL, ".
		   "dname VARCHAR(20) NOT NULL, ".
		   "maddr VARCHAR(70) NOT NULL, ".		   
		   "weight TINYINT NOT NULL, ".	
		   "mroles VARCHAR(70), ".
		   "active TINYINT NOT NULL, ".
		   "PRIMARY KEY ( mid ))";
    // use exec() because no results are returned
    $conn->exec($s_sql);
    if($newTable){echo "<br>Table 'menu' created successfully</br>";}else{echo "<br>Table 'menu' not changed</br>";}
    }
catch(PDOException $e)
    {
    echo $s_sql . "<br>Error for menu table:" . $e->getMessage();
    }
	
if(table_exist("menu", $conx)){
	$boMenu = true;		
}	

$conn = null;
$conx = null;

// ADD DEFAULT MENU ITEMS
if($boMenu && $newTable){
	//Set the query
	$o_database->query('INSERT INTO menu (mname, dname, maddr, weight, mroles, active) VALUES (:mname, :dname, :maddr, :weight, :mroles, :active)');
	//Bind the data
	$o_database->bind(':mname', 'site_config');
	$o_database->bind(':dname', 'Site Configuration');
	$o_database->bind(':maddr', 'site_config');
	$o_database->bind(':weight', '-49');
	$o_database->bind(':mroles', '1');
	$o_database->bind(':active', '1');
	//Execute the query
	$o_database->execute();
	//Bind the data
	$o_database->bind(':mname', 'home');
	$o_database->bind(':dname', 'Home');
	$o_database->bind(':maddr', 'home');
	$o_database->bind(':weight', '-50');
	$o_database->bind(':mroles', '1,2,3');
	$o_database->bind(':active', '1');
	//Execute the query
	$o_database->execute();
	//Bind the data
	$o_database->bind(':mname', 'logout');
	$o_database->bind(':dname', 'Log Out');
	$o_database->bind(':maddr', 'access-logout');
	$o_database->bind(':weight', '50');
	$o_database->bind(':mroles', '1,2,3');
	$o_database->bind(':active', '1');
	//Execute the query
	$o_database->execute();
}

if(!table_exist("wiki", $conx)){
	$newTable = true;		
}else{
	$newTable = false;
}
try {
    $conn = new PDO("$dbadapter:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$s_sql = "CREATE TABLE IF NOT EXISTS wiki( ".
		   "wid int(11) NOT NULL AUTO_INCREMENT, ".
		   "module varchar(20) NOT NULL, ".
		   "ordinal int(11) NOT NULL, ".
		   "title tinytext, ".
		   "content text, ".
		   "PRIMARY KEY (wid), ".
		   "UNIQUE KEY wid_UNIQUE (wid)); ";
    // use exec() because no results are returned
    $conn->exec($s_sql);
    if($newTable){echo "<br>Table 'wiki' created successfully</br>";}else{echo "<br>Table 'wiki' not changed</br>";}
    }
catch(PDOException $e)
    {
    echo $s_sql . "<br>Error for wiki table:" . $e->getMessage();
    }
	
$conn = null;
$conx = null;	

echo "Database creation complete.";

?>
</body>
</html>