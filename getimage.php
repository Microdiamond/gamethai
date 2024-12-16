<?php
include 'db_maintain.php';
$pmid = $_GET['pmid'];

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute SQL query to retrieve image paths based on pm_id
$sql = "SELECT image_path FROM pm_images WHERE pm_id = '$pmid'";
$result = $conn->query($sql);

// Create an array to store image paths
$imagePaths = array();

// Check if there are any results
if ($result->num_rows > 0) {
    // Fetch rows and add image paths to the array
    while ($row = $result->fetch_assoc()) {
        $imagePaths[] = $row['image_path'];
    }
}

// Close the database connection
$conn->close();

// Encode the image paths array to JSON
echo json_encode($imagePaths);
?>
