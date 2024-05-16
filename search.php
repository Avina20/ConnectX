<?php
// Include database connection
include "db_connection.php";

// Check if the keyword parameter exists in the URL
if (isset($_GET['keyword'])) {
    // Retrieve the keyword from the URL
    $keyword = $_GET['keyword'];

    // Construct the SQL query to search for messages containing the keyword
    $sql = "SELECT * FROM message WHERE body LIKE :keyword";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['keyword' => '%' . $keyword . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display search results
    if (count($results) > 0) {
        echo "<h3>Search Results:</h3>";
        foreach ($results as $result) {
            echo "<p>" . htmlspecialchars($result['body']) . "</p>";
        }
    } else {
        echo "<p>No results found.</p>";
    }
} else {
    // Redirect back to the main page if the keyword parameter is not provided
    header("Location: main_page.php");
    exit();
}
