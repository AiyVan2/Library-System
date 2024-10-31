<?php
session_start();
require_once '../Config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email and password were sent from the form
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Use prepared statements for security
        $stmt = $conn->prepare("SELECT id, email, role FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password); // Bind parameters to the statement
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            // Login successful
            $row = $result->fetch_assoc();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];

            // Redirect based on the role
            if ($row['role'] == 'admin') {
                header('Location: ../Views/Admin/Admin_Page.php');
                exit();
            } else {
                header('Location: ../Views/Student/Student_HomePage.php');
                exit();
            }
        } else {
            // Login failed, redirect or show error
            header('Location:../index.html');
        }
    }
}
?>
