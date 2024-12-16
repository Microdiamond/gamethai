<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Style for image */
        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            padding: 20px;
        }

        /* Optional: Add more styles as needed */
    </style>
</head>
<body>
    <?php
    include 'db_maintain.php';

    // รับค่า pmid จาก Query String
    if (isset($_GET['pmid'])) {
        $pmid = $_GET['pmid'];
        $sql = "SELECT * FROM pm_images WHERE pm_id = '$pmid'";
        $result = $conn->query($sql);
        // ทำการดึงข้อมูลรูปภาพจากฐานข้อมูล หรือจากไฟล์
        // เช่น โดยใช้ SQL query หรือการอ่านไฟล์จากเซิร์ฟเวอร์

        // นำเส้นทางของรูปภาพมาแสดง
        // Check if there are results
        if ($result->num_rows > 0) {
            // Fetch data and loop through each row
            while ($row = $result->fetch_assoc()) {
                // Access data from $row['column_name']
                echo "<img src=$row[image_path] alt='Image'>";
            }
        } else {
            echo "0 results";
        }
    } else {
        // หากไม่ได้รับค่า pmid ให้แสดงข้อความแจ้งเตือน
        echo "PM ID not provided.";
    } ?>
</body>

</html>