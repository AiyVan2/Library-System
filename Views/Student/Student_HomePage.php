<?php
session_start();
include '../../Config/db.php';

// Initialize search parameters
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : 'title';

// Build the SQL query based on search parameters
$base_query = "
    SELECT 
        books.*, 
        authors.name AS author_name,
        genres.genre_name 
    FROM books 
    LEFT JOIN authors ON books.author_id = authors.author_id
    LEFT JOIN genres ON books.genre_id = genres.genre_id
";

if (!empty($search_term)) {
    switch ($search_type) {
        case 'title':
            $base_query .= " WHERE books.title LIKE '%" . $conn->real_escape_string($search_term) . "%'";
            break;
        case 'author':
            $base_query .= " WHERE authors.name LIKE '%" . $conn->real_escape_string($search_term) . "%'";
            break;
        case 'genre':
            $base_query .= " WHERE genres.genre_name LIKE '%" . $conn->real_escape_string($search_term) . "%'";
            break;
        case 'year':
            $base_query .= " WHERE YEAR(books.publication_date) = '" . $conn->real_escape_string($search_term) . "'";
            break;
    }
}

$base_query .= " ORDER BY books.title";
$result = $conn->query($base_query);
$books = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Books</title>
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
            <a href="../../index.html" class="text-gray-600 hover:text-blue-600 flex items-center transition duration-200 space-x-3">
                <i class="fas fa-right-from-bracket"></i>
                <span class="mr-2">Log Out</span>
            </a>
        </div>
    </div>
</nav>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-center items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Library Books</h1>
    </div>

    <!-- Search Form -->
    <div class="mb-6 bg-white rounded-lg shadow p-6">
        <form action="" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo htmlspecialchars($search_term); ?>"
                    placeholder="Enter search term..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
            <div class="w-48">
                <select 
                    name="search_type" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="title" <?php echo $search_type === 'title' ? 'selected' : ''; ?>>Search by Title</option>
                    <option value="author" <?php echo $search_type === 'author' ? 'selected' : ''; ?>>Search by Author</option>
                    <option value="genre" <?php echo $search_type === 'genre' ? 'selected' : ''; ?>>Search by Genre</option>
                    <option value="year" <?php echo $search_type === 'year' ? 'selected' : ''; ?>>Search by Year</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button 
                    type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg flex items-center"
                >
                    <i class="fas fa-search mr-2"></i> Search
                </button>
                <a 
                    href="<?php echo $_SERVER['PHP_SELF']; ?>" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center"
                >
                    <i class="fas fa-undo mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Results count -->
    <?php if (!empty($search_term)): ?>
    <div class="mb-4 text-gray-600">
        Found <?php echo count($books); ?> result(s) for: <?php echo htmlspecialchars($search_term); ?>
    </div>
    <?php endif; ?>

    <!-- Success Message -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['message']; ?></span>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Books Table -->
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
                <?php if (count($books) > 0): ?>
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
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No books found matching your search criteria.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>