<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <title>Bootstrap Example</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    .gh {
        height: 100px
    }

    .gdp {
        height: 100px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
</style>

<body class="p-3 m-0 border-0 bd-example m-0 border-0 bd-example-cols">
    <div class="container text-center">
        <div class="row g-2">
            <div class="col-6">
                <div class="p-3 gdp">ตรวจ PM 1</div>
            </div>
            <div class="col-6">
                <div class="p-3 gdp">ตรวจ PM 2</div>
            </div>
            <div class="col-6">
                <div class="p-3 gdp">ตรวจ PM 3</div>
            </div>
            <div class="col-6">
                <div class="p-3 gdp">ตรวจ PM 4</div>
            </div>
        </div>
    </div>
    <!-- Script section to define the links() function -->
    <script>
        // Define the links() function
        function links(name) {
            switch (name) {
                case "ตรวจ PM 1":
                    // Add your logic for PM 1
                    window.location.href = "maintenace.php";
                    break;
                case "ตรวจ PM 2":
                    // Add your logic for PM 2
                    window.location.href = "equipment.php";
                    break;
                case "ตรวจ PM 3":
                    // Add your logic for PM 3
                    window.location.href = "ccrmain.php";
                    break;
                case "ตรวจ PM 4":
                    // Add your logic for PM 4
                    window.location.href = "pmnode.php";
                    break;
                default:
                    // Handle unexpected cases
                    alert("Unexpected click");
                    break;
            }

        }
        // Attach event listeners to all elements with the class "gdp"
        document.querySelectorAll('.gdp').forEach(item => {
            item.addEventListener('click', function() {
                links(this.textContent); // Call the links() function with the text content of the clicked div
            });
        });
    </script>
</body>

</html>