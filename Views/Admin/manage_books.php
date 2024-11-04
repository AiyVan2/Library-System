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
    <title>Add Book</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <nav class="bg-blue-200 shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="Admin_Page.php" class="flex items-center space-x-3 text-xl font-bold text-stone-950 hover:text-blue-600">
                    <i class="fas fa-book-reader"></i>
                    <span>Library System</span>
                </a>
                
                <a href="Books_Page.php" class="text-stone-950 hover:text-blue-600 flex items-center transition duration-200 space-x-3">
                    <i class="fas fa-arrow-left"></i>
                    <span class="mr-2">Back</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 flex justify-center">
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
            <div class="flex items-center space-x-3 mb-6">
                <i class="fas fa-book text-2xl text-blue-600"></i>
                <h2 class="text-2xl font-bold text-gray-800">Add New Book</h2>
            </div>
            
            <form action="../../Controllers/Add_Books.php" method="POST">
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Book Title</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-heading text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                required 
                                class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter book title"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="publication_date" class="block text-sm font-medium text-gray-700 mb-1">Publication Date</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                            <input 
                                type="date" 
                                id="publication_date" 
                                name="publication_date" 
                                required 
                                class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="author_id" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <select 
                                id="author_id" 
                                name="author_id" 
                                required 
                                class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Select Author</option>
                                <?php while ($author = mysqli_fetch_assoc($authors_result)) : ?>
                                    <option value="<?php echo $author['author_id']; ?>"><?php echo $author['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="genre_id" class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-bookmark text-gray-400"></i>
                            </div>
                            <select 
                                id="genre_id" 
                                name="genre_id" 
                                required 
                                class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Select Genre</option>
                                <?php while ($genre = mysqli_fetch_assoc($genres_result)) : ?>
                                    <option value="<?php echo $genre['genre_id']; ?>"><?php echo $genre['genre_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Book Description</label>
                        <div class="relative">
                            <div class="absolute top-3 left-3 pointer-events-none">
                                <i class="fas fa-align-left text-gray-400"></i>
                            </div>
                            <textarea 
                                id="description" 
                                name="description" 
                                required 
                                rows="4"
                                class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter book description"
                            ></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Book</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>