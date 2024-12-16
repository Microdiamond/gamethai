<?php
include 'db_maintain.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['imagePath'])) {
    $imagePath = $_POST['imagePath'];

    // ตรวจสอบว่าไฟล์รูปภาพมีหรือไม่
    if (file_exists($imagePath)) {
        // Prepare a DELETE statement for pm_images
        $sql_images = "DELETE FROM pm_images WHERE image_path = ?";
        $stmt_images = $conn->prepare($sql_images);
        $stmt_images->bind_param("s", $imagePath);
        // Execute the statements
        $stmt_images->execute();
        // ลบไฟล์รูปภาพ
        if (unlink($imagePath)) {

            // ส่งค่ากลับเพื่อบอกว่าการลบสำเร็จ
            echo "success";
        } else {
            // ถ้ามีปัญหาในการลบไฟล์
            echo "error";
        }
    } else {
        // ถ้าไม่พบไฟล์
        echo "not_found";
    }
} else {
    // หากไม่ได้รับคำขอ POST หรือไม่ได้รับพารามิเตอร์ imagePath
    echo "invalid_request";
}
