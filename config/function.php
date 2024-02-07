<?php

session_start();

require 'dbcon.php';

// Input field validation
function validate($inputData){

    global $conn;   
    $validatedData = mysqli_real_escape_string($conn, $inputData); 
    return trim($validatedData);
}

// Redirect from 1 page to another page with the message (status)
function redirect($url,$status){

    $_SESSION['status'] = $status;
    header('Location: '.$url);
    exit(0);
}

// Display messages or status after any process
function alertMessage(){

    if(isset($_SESSION['status'])){
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h6>'.$_SESSION['status'].'</h6>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        unset($_SESSION['status']);
    }
}

// Insert record using this function
function insert($tableName, $data){
    global $conn;

    $table = validate($tableName);

    $columns = array_keys($data);
    $values = array_values($data);

    $finalColumn = implode(',', $columns);
    $finalValues = "'".implode("','", $values)."'";

    $query = "INSERT INTO $table ($finalColumn) VALUES ($finalValues)";
    $result = mysqli_query($conn, $query);
    return $result;
}

// Update date using this function
function update($tableName, $id, $data) {
    global $conn;

    $table = validate($tableName);
    $id = validate($id);

    $updateData = [];
    foreach ($data as $column => $value) {
        $updateData[] = "$column = ?";
    }

    $updateDataString = implode(', ', $updateData);

    $query = "UPDATE $table SET $updateDataString WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Bind parameters
        $types = str_repeat('s', count($data)) . 'i';
        $bindParams = array_values($data);
        $bindParams[] = $id;

        mysqli_stmt_bind_param($stmt, $types, ...$bindParams);

        // Execute the statement
        $result = mysqli_stmt_execute($stmt);

        // Close the statement
        mysqli_stmt_close($stmt);

        return $result;
    } else {
        // Handle error if prepare fails
        return false;
    }
}


function getALL($tableName, $status = NULL){

    global $conn;

    $table = validate($tableName);
    $status = validate($status);

    if($status == 'status')
    {
        $query = "SELECT * FROM $table WHERE status='0'";
    }
    else
    {
        $query = "SELECT * FROM $table";
    }
    return mysqli_query($conn, $query);
}

function getById($tableName, $id){

    global $conn;

    $table = validate($tableName);
    $id = validate($id);

    $query = "SELECT * FROM $table WHERE id='$id' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){

        if(mysqli_num_rows($result) == 1){

            $row = mysqli_fetch_assoc($result);
            $response = [
                'status' => 200,
                'data' => $row,
                'message' => 'Record Found' 
            ];
            return $response;

        }else{
            $response = [
                'status' => 404,
                'message' => 'No Data Found' 
            ];
            return $response;
        }

    }else{
        $response = [
            'status' => 500,
            'message' => 'Something Went Wrong' 
        ];
        return $response;
    }

}

// Delete data from database using id
function delete($tableName, $id) {
    global $conn;

    $table = validate($tableName);
    $id = validate($id);

    $query = "DELETE FROM $table WHERE id=? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, 'i', $id);

        // Execute the statement
        $result = mysqli_stmt_execute($stmt);

        // Close the statement
        mysqli_stmt_close($stmt);

        return $result;
    } else {
        // Handle error if prepare fails
        return false;
    }
}


function checkParamId($type){

    if(isset($_GET[$type])){
        if($_GET[$type] != '') {
   
            return $_GET[$type];

        }else{

            return '<h5>No Id Found</h5>';
        }

    }else{
        return '<h5>No Id given </h5>';
    }
}

function logoutSession(){
    unset($_SESSION['loggedIn']);
    unset($_SESSION['loggedInUser']);
}

?>