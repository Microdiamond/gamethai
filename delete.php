<?php
include 'db_maintain.php';
// Check if the ID parameter is provided
if (isset($_GET['pmid'])) {
    // Retrieve the ID parameter
    $id = $_GET['pmid'];

    // Begin a transaction
    $conn->begin_transaction();

    $sql_imgPath = "SELECT image_path FROM pm_images WHERE pm_id = ?";
    $stmt_imgPath = $conn->prepare($sql_imgPath);
    $stmt_imgPath->bind_param("i", $id);
    $stmt_imgPath->execute();
    $result_imgPath = $stmt_imgPath->get_result();

    // Loop through the result set and delete files
    while ($imgPath_row = $result_imgPath->fetch_assoc()) {
        $imgPath = $imgPath_row['image_path'];
        // $file_path = "uploads/pmcomdatacen/15-03-2024/92D83C76-B3C5-4280-824B-9A7CEE5D9994.jpg";
        // Delete the file
        if (file_exists($imgPath)) {
            $dirname = dirname($imgPath);
            if (unlink($imgPath)) {
                echo "Deleted file: $imgPath <br>";
                // ตรวจสอบว่าโฟลเดอร์ว่างหรือไม่
                if (is_dir($dirname)) {
                    // เปิด Directory Handle
                    $handle = opendir($dirname);

                    // นับจำนวนไฟล์ในโฟลเดอร์
                    $file_count = 0;
                    while ($file = readdir($handle)) {
                        if ($file != "." && $file != "..") {
                            $file_count++;
                        }
                    }

                    // ถ้าโฟลเดอร์ว่าง
                    if ($file_count == 0) {
                        // ลบโฟลเดอร์
                        if (rmdir($dirname)) {
                            echo "Deleted directory: $dirname";
                        } else {
                            echo "Error deleting directory: $dirname";
                        }
                    } else {
                        echo "Directory is not empty.";
                    }

                    // ปิด Directory Handle
                    closedir($handle);
                } else {
                    echo "Directory does not exist.";
                }
            } else {
                echo "Error deleting file: $imgPath <br>";
            }
        } else {
            echo "File does not exist: $imgPath <br>";
        }
    }

    // Prepare a DELETE statement for pm_records
    $sql_records = "DELETE FROM pm_records WHERE pm_id = ?";
    $stmt_records = $conn->prepare($sql_records);

    // Prepare a DELETE statement for pm_images
    $sql_images = "DELETE FROM pm_images WHERE pm_id = ?";
    $stmt_images = $conn->prepare($sql_images);

    // Bind the ID parameter to both statements
    $stmt_records->bind_param("i", $id);
    $stmt_images->bind_param("i", $id);

    // Execute the statements
    $images_deleted = $stmt_images->execute();

    // Check if both deletions were successful
    if ($images_deleted) {
        $records_deleted = $stmt_records->execute();
        // Commit the transaction
        if ($records_deleted) {
            $conn->commit();
            echo json_encode(array("success" => true));
        }
    } else {
        // Rollback the transaction if any deletion fails
        $conn->rollback();
        echo json_encode(array("success" => false, "error" => "Failed to delete records"));
    }
} else {
    // ID parameter not provided
    echo json_encode(array("success" => false, "error" => "ID parameter not provided"));
}
