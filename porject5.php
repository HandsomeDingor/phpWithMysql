<?php
    $host = "localhost";
    $username = "root";
    $password = "password";
    $database = "project";

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Autocomplete logic
if (isset($_GET['term'])) {
    $searchTerm = $_GET['term'];
    $sql = "SELECT ssn From Employee";
    $result = $conn->query($sql);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row['ssn'];
    }

    // Close the database connection
    $conn->close();

    // Return data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
    exit(); // Stop further execution
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Database</title>
    <!-- Include jQuery and jQuery UI libraries -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
    <h1>Employee Database</h1>

    <?php
    // Database connection settings
    $host = "localhost";
    $username = "root";
    $password = "passowrd";
    $database = "project";

    // Create a database connection
    $conn = mysqli_connect($host, $username, $password, $database);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Function to validate SSN format (exactly 9 digits)
    function validateSSN($ssn) {
        return preg_match("/^\d{9}$/", $ssn);
    }
    
    // Handle form submissions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["showTable"])) {
            // Handle table selection
            $selectedTable = $_POST["table"];
    
            // Generate SQL query based on the selected table
            $sql = "SELECT * FROM $selectedTable";
    
            $result = mysqli_query($conn, $sql);
    
            if ($result) {
                // Display the table
                echo "<h2>$selectedTable Table</h2>";
                echo "<table border='1'>";
                echo "<tr>";
                while ($fieldInfo = mysqli_fetch_field($result)) {
                    echo "<th>{$fieldInfo->name}</th>";
                }
                echo "</tr>";
    
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else if (isset($_POST["lookup"])) {
            // Handle lookup operation
            $ssn = $_POST["ssn"];
            if (validateSSN($ssn)) {
                $sql = "SELECT * FROM Employee E INNER JOIN dependents D ON E.ssn= D.ssn WHERE E.ssn = '$ssn'";
                $result = mysqli_query($conn, $sql);
                // Display the result
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "SSN: " . $row["ssn"] . "<br>";
                        echo "First Name: " . $row["Fname"] . "<br>";
                        echo "Middle Name: " . $row["Minit"] . "<br>";
                        echo "Last Name: " . $row["Lname"] . "<br>";
                        echo "DOB: " . $row["dob"] . "<br>";
                        echo "Address: " . $row["address"] . "<br>";
                        echo "Dept#: " . $row["deptNum"] . "<br>";
                        echo "Project#: " . $row["projNum"] . "<br>";
                        echo "Project Name: " . $row["projName"] . "<br>";
                        // echo "Dependent Name: " . $row["dependentName"] . "<br>";
                        // echo "Relationship: " . $row["relationship"] . "<br>";
                    }
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "Invalid SSN format (must be 9 digits).";
            }
        } elseif (isset($_POST["insert"])) {
            // Handle insert operation
            // Collect form data
            $ssn = $_POST["ssn"];
            $dob = $_POST["dob"];
            $fname = $_POST["fname"];
            $minit = $_POST["minit"];
            $lname = $_POST["lname"];
            $address = $_POST["address"];
            $deptNum = $_POST["deptNum"];
            $projNum = $_POST["projNum"];
            $projName = $_POST["projName"];

            if (validateSSN($ssn)) {
                // Insert data into the Employee table
                $sql = "INSERT INTO Employee (ssn, dob, Fname, Minit, Lname, address, deptNum, projNum, projName)
                        VALUES ('$ssn', '$dob', '$fname', '$minit', '$lname', '$address', $deptNum, $projNum, '$projName')";

                if (mysqli_query($conn, $sql)) {
                    echo "Record inserted successfully.";
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "Invalid SSN format (must be 9 digits).";
            }
        } elseif (isset($_POST["insertD"])) {
            // Handle insert operation
            // Collect form data
            $ssn = $_POST["ssn"];
            $dependentName = $_POST["dependentName"];
            $relationship = $_POST["relationship"];

            if (validateSSN($ssn)) {
                // Insert data into the Employee table
                $sql = "INSERT INTO Dependents (ssn, dependentName, relationship)
                        VALUES ('$ssn', '$dependentName', '$relationship')";

                if (mysqli_query($conn, $sql)) {
                    echo "Record inserted successfully.";
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "Invalid SSN format (must be 9 digits).";
            }
        } elseif (isset($_POST["delete"])) {
            // Handle delete operation
            $ssn = $_POST["ssn"];
            if (validateSSN($ssn)) {
                $sql = "DELETE FROM Employee WHERE ssn = '$ssn'";
                if (mysqli_query($conn, $sql)) {
                    echo "Record deleted successfully.";
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "Invalid SSN format (must be 9 digits).";
            }
        } elseif (isset($_POST["deleteD"])) {
            // Handle delete operation
            $ssn = $_POST["ssn"];
            if (validateSSN($ssn)) {
                $sql = "DELETE FROM Dependents WHERE ssn = '$ssn'";
                if (mysqli_query($conn, $sql)) {
                    echo "Record deleted successfully.";
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "Invalid SSN format (must be 9 digits).";
            }
        } elseif (isset($_POST["update"])) {
            // Handle update operation
            // Collect form data
            $ssn = $_POST["ssn"];
            $fname = $_POST["fname"];
            $minit = $_POST["minit"];
            $lname = $_POST["lname"];
            $address = $_POST["address"];
            $deptNum = $_POST["deptNum"];
            $projNum = $_POST["projNum"];
            $projName = $_POST["projName"];

            if (validateSSN($ssn)) {
                // Update data in the Employee table
                $sql = "UPDATE Employee
                        SET Fname = '$fname', Minit = '$minit', Lname = '$lname', address = '$address', deptNum = $deptNum, projNum = $projNum, projName = '$projName'
                        WHERE ssn = '$ssn'";

                if (mysqli_query($conn, $sql)) {
                    echo "Record updated successfully.";
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "Invalid SSN format (must be 9 digits).";
            }
        } elseif (isset($_POST["updateD"])) {
            // Handle update operation
            // Collect form data
            $ssn = $_POST["ssn"];
            $dependentName = $_POST["dependentName"];
            $relationship = $_POST["relationship"];

            if (validateSSN($ssn)) {
                // Update data in the Employee table
                $sql = "UPDATE Dependents
                        SET dependentName = '$dependentName', relationship = '$relationship'
                        WHERE ssn = '$ssn'";

                if (mysqli_query($conn, $sql)) {
                    echo "Record updated successfully.";
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "Invalid SSN format (must be 9 digits).";
            }
        }
    }

    // Close the database connection
    mysqli_close($conn);
    ?>

    <h2>Select Table</h2>
    <form method="POST">
        <select name="table">
            <option value="Employee">Employee Table</option>
            <option value="Dependents">Dependents Table</option>
            <!-- Add more options for other tables as needed -->
        </select>
        <input type="submit" name="showTable" value="Show Table">
    </form>

    <h2>Lookup Employee</h2>
    <form method="POST">
        SSN: <input type="text" name="ssn" required pattern="\d{9}" title="SSN must be exactly 9 digits">
        <input type="submit" name="lookup" value="Lookup">
    </form>

    <h2>Insert Employee</h2>
    <form method="POST">
        SSN: <input type="text" id="ssn" name="ssn" required pattern="\d{9}" title="SSN must be exactly 9 digits" autocomplete="off"><br>
        DOB: <input type="date" name="dob" required><br>
        First Name: <input type="text" name="fname" required><br>
        Middle Name: <input type="text" name="minit" required><br>
        Last Name: <input type="text" name="lname" required><br>
        Address: <input type="text" name="address" required><br>
        Department Number: <input type="number" name="deptNum" required><br>
        Project Number: <input type="number" name="projNum" required><br>
        Project Name: <input type="text" name="projName" required><br>
        <input type="submit" name="insert" value="Insert">
    </form>

    <h2>Insert Dependents </h2>
    <form method="POST">
        SSN: <input type="text" id="ssn1" name="ssn" required pattern="\d{9}" title="SSN must be exactly 9 digits" autocomplete="off"><br>
        DependentName: <input type="text" name="dependentName" required><br>
        Relationship: <input type="text" name="relationship" required><br>
        <input type="submit" name="insertD" value="Insert">
    </form>

    <h2>Delete Employee</h2>
    <form method="POST">
        SSN: <input type="text" id="ssn2" name="ssn" required pattern="\d{9}" title="SSN must be exactly 9 digits" autocomplete="off">
        <input type="submit" name="delete" value="Delete">
    </form>

    <h2>Delete Dependent</h2>
    <form method="POST">
        SSN: <input type="text" id="ssn3" name="ssn" required pattern="\d{9}" title="SSN must be exactly 9 digits" autocomplete="off">
        <input type="submit" name="deleteD" value="Delete">
    </form>

    <h2>Update Employee</h2>
    <form method="POST">
        SSN: <input type="text" name="ssn" required><br>
        First Name: <input type="text" name="fname" required><br>
        Middle Name: <input type="text" name="minit" required><br>
        Last Name: <input type="text" name="lname" required><br>
        Address: <input type="text" name="address" required><br>
        Department Number: <input type="number" name="deptNum" required><br>
        Project Number: <input type="number" name="projNum" required><br>
        Project Name: <input type="text" name="projName" required><br>
        <input type="submit" name="update" value="Update">
    </form>

    <h2>Update Dependent</h2>
    <form method="POST">
        SSN: <input type="text" name="ssn" required><br>
        Dependent Name: <input type="text" name="dependentName" required><br>
        Relationship: <input type="text" name="relationship" required><br>
        <input type="submit" name="updateD" value="Update">
    </form>

    <!-- Script for Autocomplete -->
<script>
    $(document).ready(function () {
        $('#ssn').autocomplete({
            source: '<?php echo $_SERVER["PHP_SELF"] ?>', // Use the same PHP file for autocomplete
        });
    });
    $(document).ready(function () {
        $('#ssn1').autocomplete({
            source: '<?php echo $_SERVER["PHP_SELF"] ?>', // Use the same PHP file for autocomplete
        });
    });
    $(document).ready(function () {
        $('#ssn2').autocomplete({
            source: '<?php echo $_SERVER["PHP_SELF"] ?>', // Use the same PHP file for autocomplete
        });
    });
    $(document).ready(function () {
        $('#ssn3').autocomplete({
            source: '<?php echo $_SERVER["PHP_SELF"] ?>', // Use the same PHP file for autocomplete
        });
    });
</script>
</body>
</html>
