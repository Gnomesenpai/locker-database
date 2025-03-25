<?php

// static variables
$charlength='10';
$currentdate=date("Y-m-d");
$currentdatetime=date("Y-m-d H:i:s");
$clientip = $_SERVER['REMOTE_ADDR'];

// Start the session to store messages
session_start();

// Include the database connection
include('db.php');

// Start output buffering to prevent header modification warnings
ob_start();

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Attempt to fetch the record from the database
    $sql = "SELECT * FROM locker WHERE id = $id";
    $result = $conn->query($sql);

    // If the record does not exist, create a new one
    if ($result->num_rows == 0) {
        // No record found, create a new record with the provided ID
        $create_sql = "INSERT INTO locker (id, name, reference, date) 
                       VALUES ($id, '', '', '1970-01-01')"; // Use CURRENT_DATE for the date if field is NOT NULL
                       
        if ($conn->query($create_sql) === TRUE) {
            // Record created successfully, now fetch it
            $result = $conn->query("SELECT * FROM locker WHERE id = $id");
            $row = $result->fetch_assoc();
            $_SESSION['message'] = "New record created successfully with ID: $id. Please fill in the details.";
        } else {
            $_SESSION['message'] = "Error creating new record: " . $conn->error;
        }
    } else {
        // Record exists, fetch the data
        $row = $result->fetch_assoc();
    }
} else {
    // If no ID is provided in the URL
    $_SESSION['message'] = "No ID provided!";
    exit;
}

// If the form is submitted, update or create the record
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle delete request
    if (isset($_POST['delete_all'])) {
        // Clear the fields (but not the ID)
        $delete_sql = "UPDATE locker SET name = '', reference = '', date = '1970-01-01', last_modified = '$currentdatetime', ip = '$clientip' WHERE id = $id";
        if ($conn->query($delete_sql) === TRUE) {
            $_SESSION['message'] = "All fields for ID $id have been cleared!";
            // Redirect to the same page after clearing fields
            header("Location: edit.php?id=$id");
            exit;
        } else {
            $_SESSION['message'] = "Error clearing fields: " . $conn->error;
            // Don't perform a redirect here, just show the error message
        }
    }

    // Otherwise handle save (update)
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $reference = isset($_POST['reference']) ? $_POST['reference'] : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';

    // Handle the case where date is not provided, set it to CURRENT_DATE if missing
    if (empty($date)) {
        $date = date('Y-m-d');
    }

    // Ensure date format is correct
    $formatted_date = date('Y-m-d', strtotime($date));





    // Staff name input sanitization
    //limit length to 10char
    $name2 = substr($name, 0, $charlength);
    if (preg_match('/[A-Za-z]+/', $name2)) {
        $name3 = $name2;
        // change inputted name to all lowercase
        $name4 = strtolower($name3);
        // change inputted name to Ulower
        $name5 = ucfirst($name4);

    } else {
        $name5 = "INVALID INPUT";
    }

    // reference validation
    if (preg_match('/^\d{4}-\d{4}$/', $reference)) {
        $reference2 = $reference;
    } else {
        $reference2 = "INVALID INPUT";
    }

    // Update the record
    $update_sql = "UPDATE locker SET name = '$name5', reference = '$reference2', date = '$formatted_date', last_modified = '$currentdatetime', ip = '$clientip' WHERE id = $id";
    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['message'] = "Record updated successfully!";
        // Redirect to the same page to see the updated data
        header("Location: edit.php?id=$id");
        exit;
    } else {
        $_SESSION['message'] = "Error updating record: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record</title>

    <!-- Link to Sakura CSS CDN -->
    <link rel="stylesheet" href="./css/sakura.css">

    <style>
        body {
            background-color: #1e1e1e; /* Dark background */
            color: #f0f0f0; /* Light text */
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff; /* White background for the content box */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }

        h1, h2, p {
            text-align: center;
            color: #333;
        }

        label {
            font-size: 1.1em;
            margin-bottom: 10px;
            color: #333;
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            background-color: #3a3f47;
            color: #f0f0f0;
        }

        /* Save Button */
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1.1em;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            text-align: center;
            box-sizing: border-box;  /* Ensure button size is consistent */
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: #ccc;
        }

        
        .form-footer a {
            color: #4CAF50;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .delete-button {
            background-color: #3a3f47;
            color: #f0f0f0;
            padding: 10px 20px;
            font-size: 1.1em;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            text-align: center;
            box-sizing: border-box;
            margin-top: 10px;
        }

        .delete-button:hover {
/*            background-color: #c0392b; */
            background-color: #f0f0f0; !important
            border-color: #f0f0f0;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit or Create Record</h1>
    <h2>Locker number: <?php echo $id ?> </h2>
    <?php
    // Display the message from the session (if any)
    if (isset($_SESSION['message'])) {
        echo "<p>" . $_SESSION['message'] . "</p>";
        // Clear the message after displaying it
        unset($_SESSION['message']);
    }

    // Display the form with pre-filled values (or empty if creating a new record)
    // to limit reference number to 4-4, add in the input "pattern='^\d\d\d\d-\d\d\d\d$'"
    if (isset($row)) {
        echo "<form method='POST'>
                <label for='name'>Staff Name:</label>
                <input type='text' id='name' name='name' pattern='[A-Za-z]+' maxlength='$charlength' value='" . htmlspecialchars($row['name']) . "' required><br><br>
                
                <label for='reference'>Reference (e.g. 0000-0000):</label>
                <input type='text' id='reference' name='reference'  value='" . htmlspecialchars($row['reference']) . "' required><br><br> 

                <label for='date'>Date:</label>
                <input type='date' id='date' name='date' max='$currentdate' value='" . htmlspecialchars($row['date']) . "' required><br><br>

                <input type='submit' value='Save'>
              </form>";
    } else {
        echo "Failed to load data.";
    }
    ?>

    <form method="POST">
        <input type="submit" class="delete-button" name="delete_all" value="Delete All Fields">
    </form>

    <div class="form-footer">
        <p><a href="./index.php">Back to Home Page</a></p>
        <p><a href="./dashboard.php">Back to Dashboard</a></p>
    </div>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();

// End output buffering and flush the output buffer
ob_end_flush();
?>