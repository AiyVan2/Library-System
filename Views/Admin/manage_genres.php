<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Genre</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="Admin_Page.php" class="flex items-center space-x-3 text-xl font-bold text-gray-800 hover:text-blue-600">
                    <i class="fas fa-book-reader"></i>
                    <span>Library System</span>
                </a>
                
                <a href="Admin_Page.php" class="text-gray-600 hover:text-blue-600 flex items-center transition duration-200 space-x-3">
                <i class="fas fa-arrow-left"></i>
                    <span class="mr-2">Back</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 flex justify-center">
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
            <div class="flex items-center space-x-3 mb-6">
                <i class="fas fa-tags text-2xl text-blue-600"></i>
                <h2 class="text-2xl font-bold text-gray-800">Add New Genre</h2>
            </div>
            
            <form action="../../Controllers/Add_Genre.php" method="POST">
                <div class="space-y-6">
                    <div>
                        <label for="genre_name" class="block text-sm font-medium text-gray-700 mb-1">Genre Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-bookmark text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="genre_name" 
                                name="genre_name" 
                                required 
                                class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter genre name"
                            >
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Genre</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>