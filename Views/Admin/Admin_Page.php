<?php 
session_start();
include '../../Config/db.php';

// Get counts from database
$userCount = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$authorCount = $conn->query("SELECT COUNT(*) as count FROM authors")->fetch_assoc()['count'];
$bookCount = $conn->query("SELECT COUNT(*) as count FROM books")->fetch_assoc()['count'];
$genreCount = $conn->query("SELECT COUNT(*) as count FROM genres")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
   <!-- Header -->
   <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="Admin_Page.php" class="flex items-center space-x-3 text-xl font-bold text-gray-800 hover:text-blue-600">
                    <i class="fas fa-book-reader"></i>
                    <span>Library System</span>
                </a>
                <!-- <div class="flex items-center space-x-4">
                    <a href="manage_users.php" class="text-gray-600 hover:text-gray-800">Users</a>
                    <a href="manage_authors.php" class="text-gray-600 hover:text-gray-800">Authors</a>
                    <a href="manage_books.php" class="text-gray-600 hover:text-gray-800">Books</a>
                    <a href="manage_genres.php" class="text-gray-600 hover:text-gray-800">Genres</a>
                </div> -->
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Library Dashboard</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Users Card -->
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-500 rounded-full">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm text-gray-600 uppercase">Total Users</h2>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $userCount; ?></p>
                    </div>
                </div>
                <a href="Users_Page.php" class="mt-4 text-blue-500 hover:text-blue-600 text-sm flex items-center">
                    Manage Users <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Authors Card -->
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-green-500 rounded-full">
                        <i class="fas fa-pen-fancy text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm text-gray-600 uppercase">Total Authors</h2>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $authorCount; ?></p>
                    </div>
                </div>
                <a href="Authors_Page.php" class="mt-4 text-green-500 hover:text-green-600 text-sm flex items-center">
                    Manage Authors <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Books Card -->
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-500 rounded-full">
                        <i class="fas fa-book text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm text-gray-600 uppercase">Total Books</h2>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $bookCount; ?></p>
                    </div>
                </div>
                <a href="Books_Page.php" class="mt-4 text-purple-500 hover:text-purple-600 text-sm flex items-center">
                    Manage Books <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Genres Card -->
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-500 rounded-full">
                        <i class="fas fa-tags text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm text-gray-600 uppercase">Total Genres</h2>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $genreCount; ?></p>
                    </div>
                </div>
                <a href="Genres_Page.php" class="mt-4 text-yellow-500 hover:text-yellow-600 text-sm flex items-center">
                    Manage Genres <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</body>
</html>