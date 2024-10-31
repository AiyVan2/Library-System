<?php
// Include the database connection
include '../../Config/db.php';

// Fetch authors
$authors_query = "SELECT author_id, name FROM authors";
$authors_result = mysqli_query($conn, $authors_query);

// Fetch genres
$genres_query = "SELECT genre_id, genre_name FROM genres";
$genres_result = mysqli_query($conn, $genres_query);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $title = $_POST['title'];
    $publication_date = $_POST['publication_date'];
    $author_id = $_POST['author_id']; // This will still be the ID
    $genre_id = $_POST['genre_id'];   // This will still be the ID
    $description = $_POST['description'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO books (title, publication_date, author_id, genre_id, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiis", $title, $publication_date, $author_id, $genre_id, $description);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Book added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <link rel="stylesheet" href="path/to/your/styles.css"> <!-- Add your CSS file here -->
</head>
<body>
    <div class="container">
        <h2>Add New Book</h2>
        <form action="../../Controllers/Add_Books.php" method="POST">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="publication_date">Publication Date:</label>
            <input type="date" id="publication_date" name="publication_date">

            <label for="author_id">Author:</label>
            <select id="author_id" name="author_id" required>
                <option value="">Select Author</option>
                <?php while ($author = mysqli_fetch_assoc($authors_result)) : ?>
                    <option value="<?php echo $author['author_id']; ?>"><?php echo $author['name']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="genre_id">Genre:</label>
            <select id="genre_id" name="genre_id" required>
                <option value="">Select Genre</option>
                <?php while ($genre = mysqli_fetch_assoc($genres_result)) : ?>
                    <option value="<?php echo $genre['genre_id']; ?>"><?php echo $genre['genre_name']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4"></textarea>

            <button type="submit">Add Book</button>
        </form>
    </div>
</body>
</html>