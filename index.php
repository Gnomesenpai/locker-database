<?php
session_start(); // Start the session if you plan on using $_SESSION messages
// Include the database connection
include('db.php');

// Fetch all records from the locker table
$sql = "SELECT * FROM locker";
$result = $conn->query($sql);

// Initialize an empty array for search results
$results = [];

// If the form is submitted to search for a name or ID
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['name'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $sql = "SELECT * FROM locker WHERE name = '$name'";
    } elseif (!empty($_POST['id'])) {
        $id = $conn->real_escape_string($_POST['id']);
        // Redirect immediately if searching by ID
        header("Location: edit.php?id=" . urlencode($id));
        exit();
    }
    
    if (isset($sql)) {
        $query_result = $conn->query($sql);
        if ($query_result && $query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $results[] = $row;
            }
        }
    }
}
// If the form is submitted to handle a delete request (if used on this page)
// Make sure to include a hidden input for "id" in your delete form if you use this.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_all'])) {
    if (isset($_POST['id'])) {
        $id = $conn->real_escape_string($_POST['id']);
        // Clear the fields (but not the ID)
        $delete_sql = "UPDATE locker SET name = '', reference = '', date = CURRENT_DATE WHERE id = '$id'";
        if ($conn->query($delete_sql) === TRUE) {
            $_SESSION['message'] = "All fields for ID $id have been cleared!";
            header("Location: edit.php?id=" . urlencode($id));
            exit;
        } else {
            $_SESSION['message'] = "Error clearing fields: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Locker Records</title>
  <link rel="stylesheet" href="./css/sakura.css">
  <style>
        body {
            background-color: #1e1e1e;
            color: #f0f0f0;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff; /* White background for the content box */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }

        h1, h2, h3, p {
            color: #333;
        }

        label {
            font-size: 1.1em;
            margin-bottom: 10px;
            color: #333;
        }

        input[type="text"] {
            background-color: #3a3f47;
            color: #f0f0f0;
            border: 1px solid #555;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 1em;
            border-radius: 5px;


        }
        
        a.button {
            background-color: #4CAF50;
            color: #f0f0f0;
            border: 1px solid #555;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 1em;
            border-radius: 5px;
            display: inline-block;
        }

        a.button:hover {
            background-color: #4CAF50;
            border-color: #4CAF50;
            cursor: pointer;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: #f0f0f0;
            border: 1px solid #555;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 1em;
            border-radius: 5px;
            display: inline-block;
        }

        input[type="submit"]:hover {
            background-color: #4CAF50;
            border-color: #4CAF50;
            cursor: pointer;
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

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 10px;
            border: 1px solid #444;
        }

        th {
            background-color: #333;
            color: #f0f0f0;
        }

        tr:nth-child(even) {
            background-color: #3a3f47;
        }

        tr:nth-child(odd) {
            background-color: #2c2f38;
        }

        tr:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
  <div class="container">
    <h2>Locker Records</h2>
    <form method="POST">
      <label for="name">Search by Staff Name:</label>
      <input type="text" name="name" required>
      <input type="submit" value="Search">
    </form>
    <form method="POST">
        <label for="id">Enter locker number to Edit:</label>
        <input type="text" id="id" name="id" required> <!-- NUMBERS ONLY: pattern="[1-9]|[1-6]\d|7[0-5]" 1-75 -->
        <input type="submit" value="Go to Edit Page">
    </form>
    <!-- links on page -->
    <a href="./dashboard.php" class="button">Go to Dashboard Page</a>
    <a href="http://lockers:8080/" class="button">Go to DB Admin Page</a>
    <!--<a href="./export.php" class="button">Export as CSV</a>-->
    <?php if (!empty($results)): ?>
      <h3>Search Results:</h3>
      <table>
    <thead>
      <tr>
        <th>Locker ID</th>
        <th>Staff Name</th>
        <th>Reference</th>
        <th>Date</th>
        <th>Days</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($results as $row): ?>
        <!-- date conversion and calculation -->
        <?php $datetime = strtotime($row['date']); $startdate = date("d/m/Y", $datetime); $current = date('d/m/Y', time()); 
              $start = DateTime::createFromFormat('d/m/Y', $startdate); $end = DateTime::createFromFormat('d/m/Y', $current);
              $diff = $start->diff($end); $days_difference = $diff->days; ?>
        <tr>
          <td><?php echo htmlspecialchars($row['id']); ?></td>
          <td><?php echo htmlspecialchars($row['name']); ?></td>
          <td><?php echo htmlspecialchars($row['reference']); ?></td>
          <td><?php echo $startdate; ?></td>
          <td><?php echo $days_difference; ?></td>
          <td>
            <a href="edit.php?id=<?php echo urlencode($row['id']); ?>" style="color: #4CAF50;">Edit</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
      <p>No records found.</p>
    <?php else: ?>
      <h3>All Locker Records:</h3>
      <table>
        <thead>
          <tr>
            <th>Locker ID</th>
            <th>Staff Name</th>
            <th>Reference</th>
            <th>Date</th>
            <th>Days</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $datetime = strtotime($row['date']); $startdate = date("d/m/Y", $datetime); $current = date('d/m/Y', time()); 
              $start = DateTime::createFromFormat('d/m/Y', $startdate); $end = DateTime::createFromFormat('d/m/Y', $current);
              $diff = $start->diff($end); $days_difference = $diff->days;
              $display_date = ($startdate == "01/01/1970" || $days_difference > 10000) ? "" : $startdate;
              $display_days = ($startdate == "01/01/1970" || $days_difference > 10000) ? "" : $days_difference;
              /* over 180 days make the colour red */
              /* $days_style = ($display_days !== "" && $display_days > 180) ? 'style="color: orange;"' : ''; */
              $days_style = ''; //default
              if ($display_days >=180 && $display_days <=299) {
              $days_style = '"color: orange;"';
                  } elseif ($display_days >= 300) {
              $days_style = '"color: red;"';
                  }
              echo "<tr>
                      <td>" . htmlspecialchars($row['id']) . "</td>
                      <td>" . htmlspecialchars($row['name']) . "</td>
                      <td>" . htmlspecialchars($row['reference']) . "</td>
                      <td>" . $display_date . "</td>
                      <td style=" . $days_style . ">" . $display_days . "</td>
                      <td><a href='edit.php?id=" . urlencode($row["id"]) . "' style='color: #4CAF50;'>Edit</a></td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='5'>No records found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
