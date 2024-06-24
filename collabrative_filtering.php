<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sample data: User-book-category matrix
$user_books = array(
    'User1' => array('Book1' => array('Mystery', 'Thriller'), 'Book2' => array('Romance', 'Drama')),
    'User2' => array('Book1' => array('Mystery', 'Thriller'), 'Book3' => array('Fantasy', 'Adventure')),
    'User3' => array('Book2' => array('Romance', 'Drama'), 'Book4' => array('Mystery', 'Thriller')),
    'User4' => array('Book3' => array('Fantasy', 'Adventure'), 'Book4' => array('Mystery', 'Thriller'))
);

$user_id = 'User1'; // Set a valid user key from the $user_books array

// Function to recommend books based on similar categories
function generateRecommendations($user_id, $user_books, $bookGenres, $cartGenres, $numRecommendations) {
    // Replace this with your actual collaborative filtering logic
    $recommendedBooks = [];

    // Example: Generate recommended books based on genres
    foreach ($bookGenres as $book => $genre) {
        if (array($genre, $cartGenres)) {
            $recommendedBooks[] = $book;
        }
    }

    // Example: Sort and return recommended books
    return $recommendedBooks;
}

if (isset($user_books[$user_id])) {
    // Assuming you have the following data arrays populated based on your database or data source
    $bookGenres = [
        'Book1' => 'Mystery',
        'Book2' => 'Romance',
        'Book3' => 'Fantasy',
        'Book4' => 'Mystery',
        // ... Add more books and genres
    ];

    // Get the genres of books in the user's cart
    $cartGenres = array();
    foreach ($user_books[$user_id] as $bookData) {
        $cartGenres = array_merge($cartGenres, $bookData);
    }

    // Get book recommendations for the user based on their cart item genres
    $recommended_books = generateRecommendations($user_id, $user_books, $bookGenres, $cartGenres, 5);

    if (empty($recommended_books)) {
        echo "No recommendations found based on your cart items.";
    } else {
        echo "Recommended books for $user_id: " . implode(', ', $recommended_books);
    }
} else {
    echo "User ID '$user_id' not found in the user_books array.";
}
?>
