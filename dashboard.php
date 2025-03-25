<?php
// Include the database connection
include('db.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Fetch all records from the locker table
$sql = "SELECT * FROM locker";
$result = $conn->query($sql);

// auto refresh every 10 seconds
$page = $_SERVER['PHP_SELF'];
$time = "2"; // time in seconds
 header("Refresh: $time; url=$page"); 

// Count the total number of rows
$total_rows = $result->num_rows;

// Calculate the halfway point for splitting rows
$half = ceil($total_rows / 2); // Round up to ensure an even split
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Link to Sakura CSS CDN -->
    <link rel="stylesheet" href="./css/sakura.css">

    <style>
        body {
            background-color: #1e1e1e;
            color: #f0f0f0;
            /* width: auto; */
            max-width: fit-content;
            width: auto;
            /* border: solid red !important; */
        }
        a:link, a:visited {
            color: #4CAF50

        }
        .container {
            /* Making the full container a flex box ensures better scaling, and forces scrolling instead of zooming to see tiny fonts */
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;

            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff; /* White background for the content box */
            border-radius: 10px;

            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
        }

        h1, h2, p {
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

        input[type="submit"] {
            background-color: #4CAF50;
            color: #f0f0f0;
            border: 1px solid #555;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 1em;
            border-radius: 5px;
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
            width: auto;
            margin-top: 10px;
            border-collapse: collapse;
            text-align: left;
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
            width: 20em;
        }

        a.button:hover {
            background-color: #4CAF50;
            border-color: #4CAF50;
            cursor: pointer;
        }
        th, td {
            /* padding: 10px; */
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

        .table-section {
            display: flex;
            justify-content: space-between;
            gap: 10px; /* gap between tables */
            flex-wrap: wrap;
            text-wrap: nowrap;
        }

        .table-container {
            flex: 1;
        }
    </style>
</head>
<body> 
<div class="container">
    <h1>All Lockers</h1>
    <p> Page refreshes every <?php echo $time; ?> seconds </p>
    <a href="./index.php" class="button">Go to Home Page</a>
    <!-- Table sections split into four dynamically <h2>Section $section</h2> -->
    <div class="table-section">
        <?php
        $total_rows = $result->num_rows;
        $quarter = ceil($total_rows / 5);
        mysqli_data_seek($result, 0);
        
        for ($section = 1; $section <= 5; $section++) {
            echo "<div class='table-container'>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Locker</th>
                                <th>Staff Name</th>
                                <th>Reference</th>
                                <th>Start Date</th>
                                <th>Days</th>
                            </tr>
                        </thead>
                        <tbody>";
            
            $counter = 0;
            while ($row = $result->fetch_assoc()) {
                if ($counter >= ($section - 1) * $quarter && $counter < $section * $quarter) {
                    /* Date formatting */
                    $datetime = strtotime($row['date']); $startdate = date("d/m/Y", $datetime); $current = date('d/m/Y', time()); 
                    $start = DateTime::createFromFormat('d/m/Y', $startdate); $end = DateTime::createFromFormat('d/m/Y', $current);
                    $diff = $start->diff($end); $days_difference = $diff->days;
                    $gkid=$row['id'];
                    /* Hide date 01/01/1970 and anything over 10k days */
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
                    echo "<tr onclick=\"window.location.href='edit.php?id=$gkid'\" style=\"cursor: pointer;\">
                            <td><a href=edit.php?id=$gkid>" . htmlspecialchars($row['id']) . "</a></td>
                            <td>" . htmlspecialchars($row['name']) . "</td>
                            <td>" . htmlspecialchars($row['reference']) . "</td>
                            <td>" . $display_date . "</td>
                            <td style=" . $days_style . ">" . $display_days . "</td>

                          </tr></a>";
                }
                $counter++;
            }
            
            echo "</tbody>
                  </table>
                  </div>";
            
            // Reset pointer for the next section
            mysqli_data_seek($result, 0);
        }
        ?>
    </div>
</div>


</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
