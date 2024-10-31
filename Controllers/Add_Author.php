<?php
// Include the database connection
include '../Config/db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $bio = $_POST['bio'];

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT * FROM authors WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Genre already registered.");
    }

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO authors (name, bio) VALUES (?,?)");
    $stmt->bind_param("ss", $name,$bio);

    if ($stmt->execute()) {
        (header('Location: ../Views/Admin/Authors_Page.php'));
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>