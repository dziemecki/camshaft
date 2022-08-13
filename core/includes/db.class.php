<?php
/* 
This is a wrapper for the PHP PDO database class.  It simplifies interactions with the database, 
and protects against common exploits.

To instantiate a database object:
$o_database = new db(); 

To insert data:
Set the query
$o_database->query('INSERT INTO mytable (FName, LName, Age, Gender) VALUES (:fname, :lname, :age, :gender)');
Bind the data
$o_database->bind(':fname', 'John');
$o_database->bind(':lname', 'Smith');
$o_database->bind(':age', '24');
$o_database->bind(':gender', 'male');
Execute the query
$o_database->execute();

To test insert success, you can view the last insert ID before and adter the query:
echo $o_database->lastInsertId();

To update a record:
$UserID = 20;
Set the query
$o_database->query('UPDATE mytable SET FName = :fnname, LName = :lname WHERE ID = :Userid');
Bind the data
$o_database->bind(':Userid', $UserID);
$o_database->bind(':fname', 'Fastbit');
$o_database->bind(':lname', 'Informatica');
Execute the query
$o_database->execute();

To insert multiple records using a Transaction:
Begin the transaction
$o_database->beginTransaction();
Set the query
$o_database->query('INSERT INTO mytable (FName, LName, Age, Gender) VALUES (:fname, :lname, :age, :gender)');
Bind the data
$o_database->bind(':fname', 'Jenny');
$o_database->bind(':lname', 'Smith');
$o_database->bind(':age', '23');
$o_database->bind(':gender', 'female');
Execute the query
$o_database->execute();
Bind the second data set
$o_database->bind(':fname', 'Jilly');
$o_database->bind(':lname', 'Smith');
$o_database->bind(':age', '25');
$o_database->bind(':gender', 'female');
Execute the query
$o_database->execute();
Commit the transaction
$o_database->endTransaction();

To select a single row:
Set the query
$o_database->query('SELECT FName, LName, Age, Gender FROM mytable WHERE FName = :fname');
Bind the data
$o_database->bind(':fname', 'Jenny');
Run the query
$row = $o_database->single();
A row array is returned.

To select multiple rows:
Set the query
$o_database->query('SELECT FName, LName, Age, Gender FROM mytable WHERE LName = :lname');
Bind the data
$o_database->bind(':lname', 'Smith');
Run the query and save it to the $rows array
$rows = $o_database->resultset();

*/

class db{
    private $host      = _DB_HOST;
    private $user      = _DB_USER;
    private $pass      = _DB_PASS;
    private $dbname    = _DB_NAME;
 
    private $dbh;
    public $error;
	private $stmt;
 
    public function __construct(){
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
        // Create a new PDO instanace
        try{
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        }
        // Catch any errors
        catch(PDOException $e){
            $this->error = $e->getMessage();
        }
    }
	
	public function query($query){
		$this->stmt = $this->dbh->prepare($query);
	}
	
	public function bind($param, $value, $type = null){
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
    $this->stmt->bindValue($param, $value, $type);
	}
	
	public function execute(){
		return $this->stmt->execute();
	}
	
	public function resultset(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function single(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function rowCount(){
		return $this->stmt->rowCount();
	}
	
	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}
	
	public function beginTransaction(){
		return $this->dbh->beginTransaction();
	}
	
	public function endTransaction(){
		return $this->dbh->commit();
	}
	
	public function cancelTransaction(){
		return $this->dbh->rollBack();
	}
	
	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}
}
?>