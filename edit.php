<?php
include 'db_maintain.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are filled
    if (isset($_POST["dateEdit"]) || isset($_POST["locationEdit"]) || isset($_POST["statusEdit"]) || isset($_POST["pm_id"])) {

        // Set parameters and execute
        $date = $_POST["dateEdit"];
        $location = $_POST["locationEdit"];
        $status = $_POST["statusEdit"];
        $id = $_POST["pmIdEdits"];
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare and bind SQL statement
        $stmt = $conn->prepare("UPDATE pm_records SET date = ?, location = ?, status = ? WHERE pm_id = ?");
        $stmt->bind_param("sssi", $date, $location, $status, $id);
        if ($stmt->execute()) {
            // Get the ID of the last inserted record

            // Insert images into PM_Images table
            if (!empty($_FILES['images']['name'][0])) {
                $image_paths = [];
                $target_dir = "uploads/pmcomdatacen/"; // Directory to store uploaded images

                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    // $image_name = $_FILES['images']['name'][$key];
                    // $target_file = $target_dir . basename($image_name);

                    // if (move_uploaded_file($tmp_name, $target_file)) {
                    //     $image_paths[] = $target_file;
                    // } else {
                    //     echo "Error uploading image: $image_name";
                    // }
                    $image_name = $_FILES['images']['name'][$key];
                    $image_date = date("d-m-Y"); // Get current date in dd-mm-yyyy format
                    $date_folder = $target_dir . $image_date . "/"; // Subdirectory based on date
                    $target_file = $date_folder . basename($image_name);

                    // Check if the subdirectory exists, if not, create it
                    if (!file_exists($date_folder)) {
                        mkdir($date_folder, 0777, true); // Create the directory recursively
                    }

                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $image_paths[] = $target_file;
                    } else {
                        echo "Error uploading image: $image_name";
                    }
                }

                // Insert image paths into PM_Images table
                foreach ($image_paths as $image_path) {
                    $sql = "INSERT INTO PM_Images (pm_id, image_path) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $id, $image_path);
                    $stmt->execute();
                }
            }
            header("Location: maintenace.php");
            echo "Data inserted successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $conn->close();
    }
}
