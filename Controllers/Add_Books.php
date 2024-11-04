<?php
// Include the database connection
include '../Config/db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $title = $_POST['title'];
    $publication_date = $_POST['publication_date'];
    $author_id = $_POST['author_id'];
    $genre_id = $_POST['genre_id'];
    $description = $_POST['description'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO books (title, publication_date, author_id, genre_id, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiis", $title, $publication_date, $author_id, $genre_id, $description);

    // Execute the statement
    if ($stmt->execute()) {
       (header('Location: ../Views/Admin/Books_Page.php'));
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
}

$conn->close();
?>