<?php
// Include the database connection
include '../Config/db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $genre_name = $_POST['genre_name'];

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT * FROM genres WHERE genre_id = ?");
    $stmt->bind_param("s", $genre_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Genre already registered.");
    }

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO genres (genre_name) VALUES (?)");
    $stmt->bind_param("s", $genre_name);

    if ($stmt->execute()) {
        echo "Signup successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>