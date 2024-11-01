<?php
session_start();
include '../../Config/db.php';

// Handle Delete
if (isset($_POST['delete_author'])) {
    $author_id = $_POST['author_id'];
    $conn->query("DELETE FROM authors WHERE author_id = $author_id");
    $_SESSION['message'] = "Author deleted successfully";
    header('Location: manage_authors.php');
    exit();
}

// Handle Edit (AJAX)
if (isset($_POST['action']) && $_POST['action'] == 'update_author') {
    $author_id = $_POST['author_id'];
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    
    $stmt = $conn->prepare("UPDATE authors SET name = ?, bio = ? WHERE author_id = ?");
    $stmt->bind_param("ssi", $name, $bio, $author_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Author updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating author']);
    }
    exit();
}

// Fetch author details for modal (AJAX)
if (isset($_GET['action']) && $_GET['action'] == 'get_author') {
    $author_id = $_GET['author_id'];
    $stmt = $conn->prepare("SELECT * FROM authors WHERE author_id = ?");
    $stmt->bind_param("i", $author_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $author = $result->fetch_assoc();
    echo json_encode($author);
    exit();
}

// Fetch all authors
$result = $conn->query("SELECT * FROM authors ORDER BY name");
$authors = $result->fetch_all(MYSQLI_ASSOC);
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
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manage Authors</h1>
            <a href="add_author.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Add New Author
            </a>
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
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bio
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($authors as $author): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($author['name']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($author['bio']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-3">
                                    <a href="javascript:void(0)" onclick="openModal(<?php echo $author['author_id']; ?>)"
                                       class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this author?');">
                                        <input type="hidden" name="author_id" value="<?php echo $author['author_id']; ?>">
                                        <button type="submit" name="delete_author" 
                                                class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Author Modal -->
        <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Author</h3>
                    <form id="editAuthorForm" class="mt-4">
                        <input type="hidden" id="edit_author_id" name="author_id">
                        <input type="hidden" name="action" value="update_author">
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Name
                            </label>
                            <input type="text" id="edit_name" name="name" required
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="bio">
                                Bio
                            </label>
                            <textarea id="edit_bio" name="bio" rows="4"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
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
    </div>

    <script>
        function openModal(authorId) {
            // Fetch author details
            fetch(`?action=get_author&author_id=${authorId}`)
                .then(response => response.json())
                .then(author => {
                    document.getElementById('edit_author_id').value = author.author_id;
                    document.getElementById('edit_name').value = author.name;
                    document.getElementById('edit_bio').value = author.bio;
                    document.getElementById('editModal').classList.remove('hidden');
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

        document.getElementById('editAuthorForm').addEventListener('submit', function(e) {
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
                alert('An error occurred while updating the author');
            });
        });
    </script>
</body>
</html>