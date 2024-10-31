<?php
session_start();
require_once '../Config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if email and password were sent from Form
        if(isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Query to check if user exists with these credentials
        $sql = "SELECT id, email, role FROM users WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            // Login successful
            $row = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
        }
        if($row['role'] == 'admin'){
            (header('Location: ../Views/Admin/Admin_Page.php'));
        } else{
            (header('Location: ../Views/Student/Home_page.html'));
        }
    }
}
?>