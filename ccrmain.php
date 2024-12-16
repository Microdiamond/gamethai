<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preventive Maintenance (PM)</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Load DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.1/css/dataTables.dataTables.css">
    <!-- Load DateTime plugin CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.5.2/css/dataTables.dateTime.min.css">
    <!-- Load jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <!-- Load DataTables -->
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.js"></script>
    <!-- Load Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <!-- Load DateTime plugin for DataTables -->
    <script src="https://cdn.datatables.net/datetime/1.5.2/js/dataTables.dateTime.min.js"></script>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    /* Modal */
    .modal {
        /* display: flex; */
        justify-content: center;
        align-items: center;

        /* Hidden by default */
        position: fixed;
        /* Stay in place */
        z-index: 1;
        /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        /* Enable scroll if needed */
        background-color: rgba(0, 0, 0, 0.4);
        /* Black with opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        /* 10% from the top and centered horizontally */
        padding: 20px;
        border: 1px solid #888;
        width: 100%;
        /* Could be more or less, depending on screen size */
        max-width: 400px;
        /* Maximum width */
    }

    /* Close Button */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }


    h2 {
        text-align: center;
    }

    .insert-form {
        /* width: 50%; */
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }

    input[type="text"],
    input[type="date"],
    input[type="email"],
    input[type="file"],
    #location,
    #locationEdit {
        width: calc(100% - 20px);
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    button.submit-btn {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button.submit-btn:hover {
        background-color: #0056b3;
    }

    .error-message {
        color: red;
    }


    #status,
    #statusEdit,
    #locations {
        width: calc(100% - 20px);
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 16px;
    }

    select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .action-icon {
        width: 20px;
        /* กำหนดความกว้างของไอคอน */
        height: 20px;
        /* กำหนดความสูงของไอคอน */
    }

    .btn-submit {
        background-color: #59cb59;
        color: #fff;
        border: none;
        font-size: 20px;
        width: 102px;
        height: fit-content;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        cursor: pointer;
    }

    #showImage {
        display: flex;
        flex-direction: row;
        width: calc(100% - 20px);
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        row-gap: 10px;
    }

    #myModalEdit .modal-content {
        /* display: grid !important; */
        width: 100%;
        margin: 10px;
    }

    .image-container {
        position: relative;
        display: inline-block;
        width: calc(100% - 20px);
    }

    .delete-icon {
        position: absolute;
        top: 0;
        right: 0;
        cursor: pointer;
        background-color: rgba(255, 255, 255, 0.5);
        /* ตั้งค่าสีพื้นหลังของไอคอน */
        padding: 5px;
        /* ตั้งค่าการเพิ่มพื้นที่รอบไอคอน */
        border-radius: 50%;
        /* ทำให้ไอคอนเป็นวงกลม */
    }

    .delete-icon:hover {
        background-color: rgba(255, 0, 0, 0.7);
        /* ตั้งค่าสีพื้นหลังของไอคอนเมื่อโฮเวอร์ */
    }
</style>

<body>
    <div>Preventive Maintenance (PM)</div>
    <div style="display: flex;justify-content: space-between;">
        <table border="0" cellspacing="5" cellpadding="5">
            <tbody>
                <tr>
                    <td>Minimum date:</td>
                    <td><input type="text" id="min" name="min"></td>
                </tr>
                <tr>
                    <td>Maximum date:</td>
                    <td><input type="text" id="max" name="max"></td>
                </tr>
            </tbody>
        </table>
        <button class="btn-submit" onclick="openModal('myModal')"> เพิ่ม </button>
    </div>
    <table id="example" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>วันที่</th>
                <th>สถานที่ PM</th>
                <th>สถานะ</th>
                <th>ตัวดำเนินการ</th>
            </tr>
        </thead>
        <tbody>

            <?php
            include 'db_maintain.php';
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // SQL query to fetch data from the database
            $sql = "SELECT * FROM pm_records WHERE typeid = 3 ORDER BY date DESC "; // Replace 'your_table' with the actual table name

            // Execute the query
            $result = $conn->query($sql);

            // Check if there are rows returned
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['date'] . "</td>";
                    echo "<td>" . $row['location'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td>";
                    echo "<img src='./mtimage/picture.png' alt='icon1' class='action-icon' onClick=viewImage(" . $row['pm_id'] . ")>";
                    echo "<img src='./mtimage/edit.png' alt='icon2' class='action-icon' onClick=editData(" . $row['pm_id'] . ")>";
                    echo "<img src='./mtimage/trash.png' alt='icon3' class='action-icon' onClick=deleteData(" . $row['pm_id'] . ")>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "0 results";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>วันที่</th>
                <th>สถานที่ PM</th>
                <th>สถานะ</th>
                <th>ตัวดำเนินการ</th>
            </tr>
        </tfoot>
    </table>
    <!-- The Modal -->
    <div id="myModal" class="modal" style="display: none;">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>เพิ่มข้อมูล</h2>
            <form action="insert.php" method="post" enctype="multipart/form-data" class="insert-form" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <input type="text" value="3" id="typePm" name="typePm" style="position: absolute; visibility:hidden">
                    <label for="location">Location PM:</label>
                    <select id="location" name="location">
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 1">NS 1 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 1</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 2">NS 2 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 2</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 3">NS 3 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 3</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 4">NS 4 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 4</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 5">NS 5 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 5</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 6">NS 6 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 6</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 7">NS 7 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 7</option>
                        <option value="โรงเรียนเมืองพัทยา 1">โรงเรียนเมืองพัทยา 1</option>
                        <option value="โรงเรียนเมืองพัทยา 2">โรงเรียนเมืองพัทยา 2</option>
                        <option value="โรงเรียนเมืองพัทยา 3">โรงเรียนเมืองพัทยา 3</option>
                        <option value="โรงเรียนเมืองพัทยา 4">โรงเรียนเมืองพัทยา 4</option>
                        <option value="โรงเรียนเมืองพัทยา 5">โรงเรียนเมืองพัทยา 5</option>
                        <option value="โรงเรียนเมืองพัทยา 6">โรงเรียนเมืองพัทยา 6</option>
                        <option value="โรงเรียนเมืองพัทยา 7">โรงเรียนเมืองพัทยา 7</option>
                        <option value="โรงเรียนเมืองพัทยา 8">โรงเรียนเมืองพัทยา 8</option>
                        <option value="โรงเรียนเมืองพัทยา 9">โรงเรียนเมืองพัทยา 9</option>
                        <option value="โรงเรียนเมืองพัทยา 10">โรงเรียนเมืองพัทยา 10</option>
                        <option value="โรงเรียนเมืองพัทยา 11">โรงเรียนเมืองพัทยา 11</option>
                        <option value="ห้องสั่งการและควบคุมเมืองพัทยา (CCR)">ห้องสั่งการและควบคุมเมืองพัทยา (CCR)</option>
                        <option value="ศูนย์ข้อมูลหลักเมืองพัทยา (Data Center)">ศูนย์ข้อมูลหลักเมืองพัทยา (Data Center)</option>
                        <option value="อาคาร 1 ชั้นใต้ดิน ศาลาว่าการเมืองพัทยา">อาคาร 1 ชั้นใต้ดิน ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 1 ชั้น 1 ศาลาว่าการเมืองพัทยา">อาคาร 1 ชั้น 1 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 1 ชั้น 2  ศาลาว่าการเมืองพัทยา">อาคาร 1 ชั้น 1 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 1 ชั้น 3  ศาลาว่าการเมืองพัทยา">อาคาร 1 ชั้น 1 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 1 ห้องแม่บ้าน ชั้น 4 ศาลาว่าการเมืองพัทยา">อาคาร 1 ห้องแม่บ้าน ชั้น 4 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 2 ชั้น 1 ศาลาว่าการเมืองพัทยา">อาคาร 2 ชั้น 1 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 2 ชั้น 2 ศาลาว่าการเมืองพัทยา">อาคาร 2 ชั้น 2 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 2 ชั้น 3 ศาลาว่าการเมืองพัทยา">อาคาร 2 ชั้น 3 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 2 ชั้น 4 ศาลาว่าการเมืองพัทยา">อาคาร 2 ชั้น 4 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 2 ชั้น 5 ศาลาว่าการเมืองพัทยา">อาคาร 2 ชั้น 5 ศาลาว่าการเมืองพัทยา</option>
                        <option value="สถานีดับเพลิงพัทยาใต้">สถานีดับเพลิงพัทยาใต้</option>
                        <option value="ศูนย์กู้ภัยแหลมบาลีฮาย">ศูนย์กู้ภัยแหลมบาลีฮาย</option>
                        <option value="โรงพยาบาลเมืองพัทยา">โรงพยาบาลเมืองพัทยา</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="">Select status</option>
                        <option value="Active">ปกติ</option>
                        <option value="Inactive">ไม่ปกติ</option>
                        <!-- Add more options as needed -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="images">Images:</label>
                    <input type="file" id="images" name="images[]" multiple>
                </div>
                <button type="submit" class="submit-btn">บันทึก</button>
            </form>
        </div>
    </div>

    <!-- The Modal -->
    <div id="myModalEdit" class="modal" style="display: none;">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>แก้ไขข้อมูล</h2>
            <form action="edit.php" method="post" enctype="multipart/form-data" class="insert-form">
                <div class="form-group">
                    <label for="dateEdit">Date:</label>
                    <input type="date" id="dateEdit" name="dateEdit" required>
                </div>
                <div class="form-group">
                    <input type="text" value="" id="pmId" name="pmIdEdits" style="position: absolute; visibility:hidden">
                    <label for="location">Location PM:</label>
                    <select id="locationEdit">
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 1">NS 1 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 1</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 2">NS 2 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 2</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 3">NS 3 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 3</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 4">NS 4 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 4</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 5">NS 5 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 5</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 6">NS 6 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 6</option>
                        <option value="ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 7">NS 7 ศูนย์เชื่อมโยงโครงข่ายสื่อสารชุมชนโรงเรียนเมืองพัทยา NS 7</option>
                        <option value="โรงเรียนเมืองพัทยา 1">โรงเรียนเมืองพัทยา 1</option>
                        <option value="โรงเรียนเมืองพัทยา 2">โรงเรียนเมืองพัทยา 2</option>
                        <option value="โรงเรียนเมืองพัทยา 3">โรงเรียนเมืองพัทยา 3</option>
                        <option value="โรงเรียนเมืองพัทยา 4">โรงเรียนเมืองพัทยา 4</option>
                        <option value="โรงเรียนเมืองพัทยา 5">โรงเรียนเมืองพัทยา 5</option>
                        <option value="โรงเรียนเมืองพัทยา 6">โรงเรียนเมืองพัทยา 6</option>
                        <option value="โรงเรียนเมืองพัทยา 7">โรงเรียนเมืองพัทยา 7</option>
                        <option value="โรงเรียนเมืองพัทยา 8">โรงเรียนเมืองพัทยา 8</option>
                        <option value="โรงเรียนเมืองพัทยา 9">โรงเรียนเมืองพัทยา 9</option>
                        <option value="โรงเรียนเมืองพัทยา 10">โรงเรียนเมืองพัทยา 10</option>
                        <option value="โรงเรียนเมืองพัทยา 11">โรงเรียนเมืองพัทยา 11</option>
                        <option value="ห้องสั่งการและควบคุมเมืองพัทยา (CCR)">ห้องสั่งการและควบคุมเมืองพัทยา (CCR)</option>
                        <option value="ศูนย์ข้อมูลหลักเมืองพัทยา (Data Center)">ศูนย์ข้อมูลหลักเมืองพัทยา (Data Center)</option>
                        <option value="อาคาร 1 ชั้นใต้ดิน ศาลาว่าการเมืองพัทยา">อาคาร 1 ชั้นใต้ดิน ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 1 ชั้น 1 ศาลาว่าการเมืองพัทยา">อาคาร 1 ชั้น 1 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 1 ชั้น 2  ศาลาว่าการเมืองพัทยา">อาคาร 1 ชั้น 1 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 1 ชั้น 3  ศาลาว่าการเมืองพัทยา">อาคาร 1 ชั้น 1 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 1 ห้องแม่บ้าน ชั้น 4 ศาลาว่าการเมืองพัทยา">อาคาร 1 ห้องแม่บ้าน ชั้น 4 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 2 ชั้น 1 ศาลาว่าการเมืองพัทยา">อาคาร 2 ชั้น 1 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 2 ชั้น 2 ศาลาว่าการเมืองพัทยา">อาคาร 2 ชั้น 2 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 2 ชั้น 3 ศาลาว่าการเมืองพัทยา">อาคาร 2 ชั้น 3 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 2 ชั้น 4 ศาลาว่าการเมืองพัทยา">อาคาร 2 ชั้น 4 ศาลาว่าการเมืองพัทยา</option>
                        <option value="อาคาร 2 ชั้น 5 ศาลาว่าการเมืองพัทยา">อาคาร 2 ชั้น 5 ศาลาว่าการเมืองพัทยา</option>
                        <option value="สถานีดับเพลิงพัทยาใต้">สถานีดับเพลิงพัทยาใต้</option>
                        <option value="ศูนย์กู้ภัยแหลมบาลีฮาย">ศูนย์กู้ภัยแหลมบาลีฮาย</option>
                        <option value="โรงพยาบาลเมืองพัทยา">โรงพยาบาลเมืองพัทยา</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="statusEdit" name="statusEdit">
                        <option value="">Select status</option>
                        <option value="Active">ปกติ</option>
                        <option value="Inactive">ไม่ปกติ</option>
                        <!-- Add more options as needed -->
                    </select>
                </div>
                <div class="form-group">
                    <!-- เพิ่ม div สำหรับแสดงรูปภาพ -->
                    <label for="images">Images:</label>
                    <input type="file" id="images" name="images[]" multiple>
                    <div id="showImage"></div>
                </div>
                <button type="submit" class="submit-btn">บันทึก</button>
            </form>
        </div>
    </div>
    <script>
        // Function to validate the form
        function validateForm() {
            // Get the form elements
            var date = document.getElementById("date").value;
            var location = document.getElementById("location").value;
            var status = document.getElementById("status").value;
            var images = document.getElementById("images").files;

            // Check if date is empty
            if (date === "") {
                alert("กรุณากรอกวันที่");
                return false;
            }

            // Check if location is empty
            if (location === "") {
                alert("กรุณาเลือกสถานที่");
                return false;
            }

            // Check if status is not selected
            if (status === "") {
                alert("กรุณาเลือกสถานะ");
                return false;
            }

            // Check if images are selected
            if (images.length === 0) {
                alert("กรุณาเลือกรูปภาพ");
                return false;
            }

            // If all validations pass, return true
            return true;
        }

        let minDate, maxDate;

        // Custom filtering function which will search data in column four between two values
        DataTable.ext.search.push(function(settings, data, dataIndex) {
            let min = minDate.val();
            let max = maxDate.val();
            let date = new Date(data[0]);

            if (
                (min === null && max === null) ||
                (min === null && date <= max) ||
                (min <= date && max === null) ||
                (min <= date && date <= max)
            ) {
                return true;
            }
            return false;
        });

        // Create date inputs
        minDate = new DateTime('#min', {
            format: 'MMMM Do YYYY'
        });
        maxDate = new DateTime('#max', {
            format: 'MMMM Do YYYY'
        });

        // DataTables initialisation
        let table = new DataTable('#example');
        // เรียงลำดับตามคอลัมน์แรก (index 0) ในลำดับจากมากไปน้อย (descending order)
        table.order([
            []
        ]).draw();
        // Refilter the table
        document.querySelectorAll('#min, #max').forEach((el) => {
            el.addEventListener('change', () => table.draw());
        });


        // Get the modal

        // Get the button that opens the modal
        var btn = document.querySelector("button");
        var modal
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        function openModal(params) {
            modal = document.getElementById(params);
            // alert('check');
            modal.style.display = "flex";
        }

        // When the user clicks on <span> (x), close the modal
        function closeModal() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function viewImage(pmid) {
            // ส่งค่า pmid ไปยังหน้า Show_image.php โดยใช้ Query String
            window.location.href = "Show_image.php?pmid=" + pmid;
        }

        // ประกาศฟังก์ชัน sendData() เพื่อส่งข้อมูลแก้ไข PM ไปยัง edit.php
        function sendData(formData) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'edit.php', true);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // ดำเนินการหลังจากส่งข้อมูลเสร็จสมบูรณ์
                        console.log(xhr.responseText);
                        // อาจเพิ่มโค้ดสำหรับปรับปรุง UI หรือการแจ้งเตือนผู้ใช้
                    } else {
                        // การส่งข้อมูลไม่สำเร็จ
                        console.error('เกิดข้อผิดพลาดในการส่งข้อมูล');
                    }
                }
            };

            xhr.send(formData);
        }

        function editData(pmid) {
            modal = document.getElementById('myModalEdit');
            // alert('check');
            modal.style.display = "grid";
            var pm_id = document.getElementById('pmId');
            pm_id.value = pmid
            var url = 'showrecord.php?pmid=' + pmid;
            var xhr = new XMLHttpRequest();
            // Configure the request (GET method, URL, asynchronous)
            xhr.open('GET', url, true);
            xhr.onload = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Parse the JSON response
                        var data = JSON.parse(xhr.responseText);

                        // Access the data and update the HTML content
                        if (data.length > 0) {
                            var output = '';
                            // Assuming pm_image_path is the key for the image path in each object
                            var lastDate = data[data.length - 1].date;
                            var location = data[data.length - 1].location;
                            var status = data[data.length - 1].status;
                            document.getElementById('dateEdit').value = lastDate;
                            document.getElementById('locationEdit').value = location;
                            document.getElementById('statusEdit').value = status;

                            // Create a new XMLHttpRequest to fetch image paths
                            var imgUrl = 'getimage.php?pmid=' + pmid;
                            var imageXhr = new XMLHttpRequest();
                            imageXhr.onreadystatechange = function() {
                                if (imageXhr.readyState === XMLHttpRequest.DONE) {
                                    if (imageXhr.status === 200) {
                                        // Parse the JSON response for image paths
                                        var imagePaths = JSON.parse(imageXhr.responseText);

                                        // Generate HTML for displaying images
                                        var imageOutput = '';
                                        for (var i = 0; i < imagePaths.length; i++) {
                                            // Assuming each object in the array has a key named 'imagePath'
                                            var imagePath = imagePaths[i];
                                            var cImage = 'imagePaths' + i;

                                            // Add an image tag for each image path
                                            imageOutput += '<div class="image-container" id="' + cImage + '">';
                                            imageOutput += '<img src="' + imagePath + '" alt="Image" style="width:inherit">';
                                            imageOutput += '<div class="delete-icon" onclick="deleteImage(\'' + imagePath + '\', \'' + cImage + '\', \'' + pmid + '\')">X</div>';
                                            imageOutput += '</div>';
                                        }

                                        // Update the HTML content in the showImage div
                                        document.getElementById('showImage').innerHTML = imageOutput;
                                    } else {
                                        console.error('Error fetching image data: ' + imageXhr.status);
                                    }
                                }
                            };

                            // Open and send request to show_image.php
                            imageXhr.open('GET', imgUrl, true);
                            imageXhr.send();
                        }
                    } else {
                        console.error('Error: ' + xhr.status);
                    }
                }
            };
            // Send the request
            xhr.send();
        }

        function deleteData(params) {
            // Confirm deletion
            if (confirm('ต้องการลบข้อมูลจริงไหม?')) {
                var url = 'delete.php?pmid=' + params;
                var xhr = new XMLHttpRequest();
                // Configure the request (GET method, URL, asynchronous)
                xhr.open('GET', url, true);
                xhr.onload = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // คำสั่งที่ต้องการให้ทำหลังจากลบข้อมูลสำเร็จ
                            console.log('Record deleted successfully');
                            location.reload()
                        } else {
                            console.error('Error: ' + xhr.status);
                        }
                    }
                };
                // Send the request
                xhr.send();
            }
        }

        function deleteImage(imagePath, cImage, pmid) {
            if (confirm("คุณแน่ใจหรือไม่ว่าต้องการลบรูปภาพนี้?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "deleteimage.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if (xhr.responseText.trim() === "success") {
                            // ทำสิ่งที่ต้องการหลังจากลบรูปภาพ
                            // เช่น ลบรูปภาพออกจาก DOM
                            document.getElementById(cImage).style.display = "none";
                            var imageContainers = document.querySelectorAll('#showImage .image-container');
                            imageContainers.forEach(function(container) {
                                if (container.querySelector('img').src === imagePath) {
                                    container.remove();
                                }
                            });
                        } else {
                            alert(xhr.responseText);
                        }
                    }
                };

                xhr.send("imagePath=" + encodeURIComponent(imagePath));
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>