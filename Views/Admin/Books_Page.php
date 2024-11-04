<?php
session_start();
include '../../Config/db.php';

// Initialize search parameters
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : 'title';

// Handle Delete
if (isset($_POST['delete_book'])) {
    $book_id = $_POST['book_id'];
    $conn->query("DELETE FROM books WHERE book_id = $book_id");
    $_SESSION['message'] = "Book deleted successfully";
    header('Location: Books_Page.php');
    exit();
}

// Handle Edit (AJAX)
if (isset($_POST['action']) && $_POST['action'] == 'update_book') {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author_id = $_POST['author_id'];
    $genre_id = $_POST['genre_id'];
    $publication_date = $_POST['publication_date'];
    $description = $_POST['description'];
    
    $stmt = $conn->prepare("UPDATE books SET title = ?, author_id = ?, genre_id = ?, publication_date = ?, description = ? WHERE book_id = ?");
    $stmt->bind_param("siissi", $title, $author_id, $genre_id, $publication_date, $description, $book_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Book updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating book']);
    }
    exit();
}

// Fetch book details for modal (AJAX)
if (isset($_GET['action']) && $_GET['action'] == 'get_book') {
    $book_id = $_GET['book_id'];
    $stmt = $conn->prepare("
        SELECT 
            books.*, 
            authors.name AS author_name,
            genres.genre_name 
        FROM books 
        LEFT JOIN authors ON books.author_id = authors.author_id
        LEFT JOIN genres ON books.genre_id = genres.genre_id
        WHERE books.book_id = ?
    ");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    echo json_encode($book);
    exit();
}

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

// Fetch authors and genres for dropdowns
$authors = $conn->query("SELECT author_id, name FROM authors ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$genres = $conn->query("SELECT genre_id, genre_name FROM genres ORDER BY genre_name")->fetch_all(MYSQLI_ASSOC);
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
<nav class="bg-blue-200 shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="Admin_Page.php" class="flex items-center space-x-3 text-xl font-bold text-stone-950 hover:text-blue-600">
                    <i class="fas fa-book-reader"></i>
                    <span>Library System</span>
                </a>
                <!--<div class="flex items-center space-x-4">
                    <a href="manage_users.php" class="text-gray-600 hover:text-gray-800">Users</a>
                    <a href="manage_authors.php" class="text-gray-600 hover:text-gray-800">Authors</a>
                    <a href="manage_books.php" class="text-gray-600 hover:text-gray-800">Books</a>
                    <a href="manage_genres.php" class="text-gray-600 hover:text-gray-800">Genres</a>
                </div> -->
            </div>
        </div>
    </nav>
    

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manage Books</h1>
            <a href="manage_books.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Add New Books
            </a>
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
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Action
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-3">
                                    <a href="javascript:void(0)" onclick="openModal(<?php echo $book['book_id']; ?>)"
                                       class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this book?');">
                                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                        <button type="submit" name="delete_book" 
                                                class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No books found matching your search criteria.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

     <!-- Add Edit Modal -->
     <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Book</h3>
                <form id="editBookForm" class="mt-4">
                    <input type="hidden" id="edit_book_id" name="book_id">
                    <input type="hidden" name="action" value="update_book">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_title">
                            Title
                        </label>
                        <input type="text" id="edit_title" name="title" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_author_id">
                            Author
                        </label>
                        <select id="edit_author_id" name="author_id" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <?php foreach ($authors as $author): ?>
                                <option value="<?php echo $author['author_id']; ?>">
                                    <?php echo htmlspecialchars($author['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_genre_id">
                            Genre
                        </label>
                        <select id="edit_genre_id" name="genre_id" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?php echo $genre['genre_id']; ?>">
                                    <?php echo htmlspecialchars($genre['genre_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_publication_date">
                            Publication Date
                        </label>
                        <input type="date" id="edit_publication_date" name="publication_date" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_description">
                            Description
                        </label>
                        <textarea id="edit_description" name="description"
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                  rows="4"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()"
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</body>
<!-- Add JavaScript for modal handling -->
<script>
        function openModal(bookId) {
            // Fetch book details
            fetch(`?action=get_book&book_id=${bookId}`)
                .then(response => response.json())
                .then(book => {
                    document.getElementById('edit_book_id').value = book.book_id;
                    document.getElementById('edit_title').value = book.title;
                    document.getElementById('edit_author_id').value = book.author_id;
                    document.getElementById('edit_genre_id').value = book.genre_id;
                    document.getElementById('edit_publication_date').value = book.publication_date;
                    document.getElementById('edit_description').value = book.description;
                    document.getElementById('editModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching book details');
                });
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('editBookForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the book');
            });
        });
    </script>
</html>