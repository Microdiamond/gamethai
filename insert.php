<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo $_POST["date"] . $_POST["location"] . $_POST["status"] . $_POST["typePm"];
    // Check if all required fields are filled
    if (isset($_POST["date"]) && isset($_POST["location"]) && isset($_POST["status"]) && isset($_POST["typePm"])) {
        // Include your database connection file
        include 'db_maintain.php';

        // Check connection
        if ($conn) {
            // Set parameters and execute
            $date = $_POST["date"];
            $location = $_POST["location"];
            $status = $_POST["status"];
            $typePm = $_POST["typePm"];

            // Prepare and bind SQL statement
            if ($typePm == 1) {
                $stmt = $conn->prepare("INSERT INTO pm_records (date, location, status, typeid) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $date, $location, $status, $typePm);
                $target_dir = "uploads/pmcomdatacen/"; // Directory to store uploaded images
            } else if ($typePm == 2) {
                $stmt = $conn->prepare("INSERT INTO pm_records (date, location, status, typeid) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $date, $location, $status, $typePm);
                $target_dir = "uploads/pmallequipment/"; // Directory to store uploaded images
            } else if ($typePm == 3) {
                $stmt = $conn->prepare("INSERT INTO pm_records (date, location, status, typeid) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $date, $location, $status, $typePm);
                $target_dir = "uploads/pmccr/"; // Directory to store uploaded images
            } else if ($typePm == 4) {
                $stmt = $conn->prepare("INSERT INTO pm_records (date, location, status, typeid) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $date, $location, $status, $typePm);
                $target_dir = "uploads/pmnode/"; // Directory to store uploaded images
            }

            if ($stmt->execute()) {
                // Get the ID of the last inserted record
                $pm_id = $conn->insert_id;

                // Insert images into PM_Images table
                if (!empty($_FILES['images']['name'][0])) {
                    $image_paths = [];

                    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
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
                        $stmt->bind_param("is", $pm_id, $image_path);
                        $stmt->execute();
                    }
                }

                // Redirect to maintenace.php after successful insertion
                if ($typePm == 1) {
                    header("Location: maintenace.php");
                } else if ($typePm == 2) {
                    header("Location: equipment.php");
                } else if ($typePm == 3) {
                    header("Location: ccrmain.php");
                } else if ($typePm == 4) {
                    header("Location: pmnode.php");
                }
                exit(); // Terminate script after redirection
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            // Close connection
            $conn->close();
        } else {
            die("Connection failed: " . mysqli_connect_error());
        }
    } else {
        // If all required fields are not filled, show an error message
        echo "Please fill all required fields.";
    }
}
