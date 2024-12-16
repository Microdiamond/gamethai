<?php 
include 'db_maintain.php';
// รับค่า pmid จาก Query String
if (isset($_GET['pmid'])) {
    $pmid = $_GET['pmid'];
    $sql = "SELECT * FROM pm_records WHERE pm_id = '$pmid'";
    $result = $conn->query($sql);

    // Check if there are results
    if ($result->num_rows > 0) {
        // Initialize an empty array to store the results
        $data = array();

        // Fetch data and loop through each row
        while ($row = $result->fetch_assoc()) {
            // Add each row to the data array
            $data[] = $row;
        }

        // Encode the data array to JSON format
        $json = json_encode($data);

        // Output the JSON data
        echo $json;
    } else {
        echo "0 results";
    }
} else {
    // หากไม่ได้รับค่า pmid ให้แสดงข้อความแจ้งเตือน
    echo "PM ID not provided.";
}

// Close the database connection
$conn->close();
?>