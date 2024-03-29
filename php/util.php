<?php
    require_once './db.php';

    // function to check for user if logged-in
    function isUserLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // function to check for user in db if exist by email
    function doesUserExistByEmail($email) {
        global $dbConnection;

        $sqlQuery = "SELECT * FROM user WHERE email=:email";
        $statement = $dbConnection->prepare($sqlQuery);
        $statement->bindParam(':email', $email);
        if($statement->execute()){
            $user = $statement->fetch(PDO::FETCH_ASSOC);
            return $user !==  false;
        }
        return false;
    }

    // function to check for user in db and find by email and password
    function findUserByEmailAndPassword($email, $password) {
        global $dbConnection;

        $sqlQuery = "SELECT * FROM user WHERE email=:email AND password=:password";

        $encryptedPassword = md5($password);
        
        $statement = $dbConnection->prepare($sqlQuery);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':password', $encryptedPassword);

        if ($statement->execute()) {
           $user = $statement->fetch(PDO::FETCH_ASSOC);
           if($user !== false){
                return $user;
           }
        }
        return null;
    }

    // function for storing user to database
    function storeUserToDatabase(array $user) {
        global $dbConnection;

        $sqlQuery = "INSERT INTO `user` (`full_name`, `email`, `password`) VALUES (:fullName, :email, :password)";

        $encryptedPassword = md5($user['password']);
		
        $statement = $dbConnection->prepare($sqlQuery);
        $statement->bindParam(":fullName", $user['full_name']);
        $statement->bindParam(":email", $user['email']);
        $statement->bindParam(":password", $encryptedPassword);
            
        if($statement->execute()) {
            return true;
        } else{
            return false;
        }
    }

    // function to make user signed-out
    function signOut() {
        session_start();
        session_destroy();
    }

    // function for storing task to database
    function storeTaskToDatabase(array $task) {
        global $dbConnection;

        $sqlQuery = "INSERT INTO `task` (`title`, `description`, `status`, `id`) VALUES (:title, :description, :status, :id)";

        $statement = $dbConnection->prepare($sqlQuery);
        $statement->bindparam(":title", $task['title']);
        $statement->bindParam(":description", $task['description']);
        $statement->bindParam(":status", $task['status']);
        $statement->bindParam(":id", $_SESSION['id']);

        if ($statement->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // function for getting task from database
    function getTaskFromDatabase() {
        global $dbConnection;
		
        $sqlQuery = "SELECT * FROM `task` WHERE `id` = " . $_SESSION['id'];

        $statement = $dbConnection->prepare($sqlQuery);

        if ($statement->execute()) {
            return $statement->fetchAll(PDO::FETCH_ASSOC);;
        } else {
            return [];
        }
    }

    // function for deleting task from database
    function deleteTaskFromDatabase($id_task) {
        global $dbConnection;

        $sqlQuery = "DELETE FROM `task` WHERE `id_task` = :id_task";

        $statement = $dbConnection->prepare($sqlQuery);
        $statement->bindparam(":id_task", $id_task);

        if ($statement->execute()) {
           return true;
        } else {
            return false;
        }
    }
?>