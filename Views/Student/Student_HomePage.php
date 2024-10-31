<?php
session_start();
include '../../Config/db.php';

// Modified query to join with authors and genres tables
$result = $conn->query("
    SELECT 
        books.*, 
        authors.name AS author_name,
        genres.genre_name 
    FROM books 
    LEFT JOIN authors ON books.author_id = authors.author_id
    LEFT JOIN genres ON books.genre_id = genres.genre_id
    ORDER BY books.title
");
$books = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Authors</title>
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
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-center items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Library Books</h1>
        </div>

        <!-- Success Message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $_SESSION['message']; ?></span>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Authors Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Book Title
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Publication Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Author
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Genre
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($books as $book): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($book['title']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 line-clamp-2"><?php echo htmlspecialchars($book['publication_date']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 line-clamp-2"><?php echo htmlspecialchars($book['author_name']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 line-clamp-2"><?php echo htmlspecialchars($book['genre_name']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 line-clamp-2"><?php echo htmlspecialchars($book['description']); ?></div>
                            </td>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>