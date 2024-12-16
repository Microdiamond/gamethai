<?php
include 'db.php';

// Fetch data from the database
$sql = "SELECT * FROM information";
$result = $conn->query($sql);

// Check if there are rows in the result
if ($result->num_rows > 0) {
    // Fetch the result set as an associative array
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Return data as JSON
    echo json_encode($data);
} else {
    // Return an empty array if no data is found
    echo json_encode([]);
}

// Close the database connection
$conn->close();
