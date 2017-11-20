<?php
//by pg395
//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);
define('DATABASE', 'pg395');
define('USERNAME', 'pg395');
define('PASSWORD', 'r4Matf7xW');
define('CONNECTION', 'sql.njit.edu');
class dbConn{
    //variable to hold connection object.
    protected static $dbConnection;
    //private construct - class cannot be instatiated externally.
    private function __construct() {
        try {
            // assign PDO object to dbConnection variable
            self::$dbConnection = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$dbConnection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
		catch (PDOException $excep) {
			//Output error - would normally log this to error file rather than output to user.
            echo "The server gave error as: ".$excep->getMessage()."</br>";
        
            
            
        }
    }
    // get connection function. Static method - accessible without instantiation
    public static function getConnection() {
        //Guarantees single instance, if no connection object exists then create one.
        if (!self::$dbConnection) {
            //new connection object.
            new dbConn();
        }
        //return connection.
        return self::$dbConnection;
    }
}
class collection {

    static public function create() {
      $model = new static::$modelName;
      return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        
        
        
         if(static::$modelName=='todo'){
         echo "<table border=\"1\"><tr><th>id</th><th>owneremail</th><th>ownerid</th><th>createddate</th><th>duedate</th><th>message</th><th>isdone</th></tr>";
         }
         else{
         echo "<table border=\"1\"><tr><th>id</th><th>email</th><th>fname</th><th>lname</th><th>phone</th><th>birthday</th><th>gender</th><th>password</th></tr>";
         }
         
         foreach($recordsSet as $tempRecord){
         if(static::$modelName=='todo'){
         echo "<tr><td>".$tempRecord->id."</td><td>".$tempRecord->owneremail."</td><td>".$tempRecord->ownerid."</td><td>".$tempRecord->createddate."</td><td>".$tempRecord->duedate."</td><td>".$tempRecord->message."</td><td>".$tempRecord->isdone."</td></tr>";
         }
         
         else{
echo "<tr><td>".$tempRecord->id."</td><td>".$tempRecord->email."</td><td>".$tempRecord->fname."</td><td>".$tempRecord->lname."</td><td>".$tempRecord->phone."</td><td>".$tempRecord->birthday."</td><td>".$tempRecord->gender."</td><td>".$tempRecord->password."</td></tr>";
             }
}
echo "</table>";
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        
         if(static::$modelName=='todo'){
         echo "<table border=\"1\"><tr><th>id</th><th>owneremail</th><th>ownerid</th><th>createdate</th><th>duedate</th><th>message</th><th>isdone</th></tr>";
         }
         else{
         echo "<table border=\"1\"><tr><th>id</th><th>email</th><th>fname</th><th>lname</th><th>phone</th><th>birthday</th><th>gender</th><th>password</th></tr>";
         }
         

          if(static::$modelName=='todo'){
         echo "<tr><td>".$recordsSet[0]->id."</td><td>".$recordsSet[0]->owneremail."</td><td>".$recordsSet[0]->ownerid."</td><td>".$recordsSet[0]->createddate."</td><td>".$recordsSet[0]->duedate."</td><td>".$recordsSet[0]->message."</td><td>".$recordsSet[0]->isdone."</td></tr>";
         }
         
         else{
echo "<tr><td>".$recordsSet[0]->id."</td><td>".$recordsSet[0]->email."</td><td>".$recordsSet[0]->fname."</td><td>".$recordsSet[0]->lname."</td><td>".$recordsSet[0]->phone."</td><td>".$recordsSet[0]->birthday."</td><td>".$recordsSet[0]->gender."</td><td>".$recordsSet[0]->password."</td></tr>";
             }

echo "</table>";
        return $recordsSet[0];
    }
}
class accounts extends collection {
    protected static $modelName = 'account';
}
class todos extends collection {
    protected static $modelName = 'todo';
}
class model {
    protected $tableName;
    
    public function save()
    {  //echo 'Id is'.$this->id;
        
        if ($this->action =='insert') {
            $sql = $this->insert();
        } elseif($this->action=='delete'){
           $sql = $this->delete();
        }
        else {
            $sql = $this->update();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
        $tableName = get_called_class();
        $array = get_object_vars($this);
        $columnString = implode(',', $array);
        $valueString = ":".implode(',:', $array);
       // echo "INSERT INTO $tableName (" . $columnString . ") VALUES (" . $valueString . ")</br>";
        echo 'I just saved record: ' . $this->id;
    }
    
    
    
    private function insert() {
       // echo 'table name in insert ';
        if($this->tableName=='todos'){
		$sql = "Insert into ".$this->tableName." values(".$this->id.",'".$this->owneremail."',".$this->ownerid.",Date('".$this->createddate."'),Date('".$this->duedate."'),'".$this->message."',".$this->isdone.")";
        }
        else{
    $sql = "Insert into ".$this->tableName." values(".$this->id.",'".$this->email."','".$this->fname."','".$this->lname."','".$this->phone."',Date('".$this->birthday."'),'".$this->gender."','".$this->password."')";
        }    
        return $sql;        
    }
    
    
    
    private function update() {
    //echo 'table name in update'.$this->id;
    if($this->tableName=='todos'){
        $sql = "Update ".$this->tableName." set owneremail='".$this->owneremail."',message = '".$this->message."' where id = ".$this->id;
        }
    else{
        $sql = "Update ".$this->tableName." set email='".$this->email."',password = '".$this->password."' where id = ".$this->id;    
        }    
        return $sql;
        echo 'I just updated record' . $this->id;
    }
    
    
    
    public function delete() {
    $sql = "Delete from ".$this->tableName." where id = ".$this->id;
    return $sql;
        echo 'I just deleted record' . $this->id;
    }
}
class account extends model {
    
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    public $action;
    public function __construct()
    {
        $this->tableName = 'accounts';
    }
}
class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public $action;

    public function __construct()
    {
        $this->tableName = 'todos';
         
    }
}

echo " Displaying all records of table Accounts </br>";
accounts::findAll();
echo " </br> ----------------------------------------------------------------------------------------------------------------------------------------------------------</br>";

echo " </br>Displaying all records of table Todos </br>";
todos::findAll();
echo " </br> ----------------------------------------------------------------------------------------------------------------------------------------------------------</br>"; 

echo " </br>Displaying record with id 4 of table Todos </br>";
$todoRecord = todos::findOne(4);
echo " </br> ----------------------------------------------------------------------------------------------------------------------------------------------------------</br>";

echo " </br>Displaying record with id 2 table Accounts </br>";
$accountRecord = accounts::findOne(2);
echo " </br> ----------------------------------------------------------------------------------------------------------------------------------------------------------</br>";

$newTodo = new todo();
$newAccount = new account();

$newTodo->action = 'insert';
$newTodo->id =9;
    $newTodo->owneremail='xyzzz@gmail.com';
    $newTodo->ownerid = 420;
    $newTodo->createddate='2017-06-15 09:34:21';
    $newTodo->duedate = '2018-09-15 09:34:21';
     $newTodo->message = 'new Item';
     $newTodo->isdone = 0;
    $newTodo->save();
    
echo " </br>Inserted new record with id 9 in table Todos </br>";    
todos::findAll();
echo " </br> ----------------------------------------------------------------------------------------------------------------------------------------------------------</br>";

$newAccount->action = 'insert';
$newAccount->id =15;
    $newAccount->email='xyzzz@gmail.com';
    $newAccount->fname = 'x';
    $newAccount->lname = 'y';
    $newAccount->phone = '12103748596';
    $newAccount->birthday = '1990-02-03 09:34:21';
    $newAccount->gender = 'male';
    $newAccount->password = 'password';
 $newAccount->save();

echo " </br>Inserted new record with id 15 in table Accounts </br>";
accounts::findAll();
echo " </br> ----------------------------------------------------------------------------------------------------------------------------------------------------------</br>";


$upDateTodo = new todo();
$upDateTodo->action = 'update';
$upDateTodo->id = 9;
$upDateTodo->owneremail = 'xyz1@gmail.com'; 
$upDateTodo->message = 'Updated Item';
$upDateTodo->save();


echo " </br>Updated record with id 9 in table Todos </br>";
todos::findAll();
echo " </br> ----------------------------------------------------------------------------------------------------------------------------------------------------------</br>";


$upDateAccount = new account();
$upDateAccount->action ='update';
$upDateAccount->id = 15;
$upDateAccount->email = 'xyz1@gmail.com'; 
$upDateAccount->password = 'Updated password';
$upDateAccount->save();

echo " </br>Updated record with id 15 in table Accounts </br>";
accounts::findAll();
echo " </br> ----------------------------------------------------------------------------------------------------------------------------------------------------------</br>";


$deleteTodo = new todo();
$deleteTodo->action ='delete';
$deleteTodo->id = 9;
$deleteTodo->save();

echo " </br>Deleted new record with id 9 in table Todos </br>";
todos::findAll();
echo " </br> ----------------------------------------------------------------------------------------------------------------------------------------------------------</br>";


$deleteAccount = new account();
$deleteAccount->action = 'delete';
$deleteAccount->id = 15;
$deleteAccount->save();

echo " </br>Deleted new record with id 15 in table Accounts </br>";
accounts::findAll();
echo " </br> ----------------------------------------------------------------------------------------------------------------------------------------------------------</br>";




?>