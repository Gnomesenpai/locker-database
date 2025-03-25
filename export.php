<?php
// Database connection
include('db.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set headers for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=database_export.csv');

// Create a file pointer connected to output
$output = fopen('php://output', 'w');

// Output column headings
fputcsv($output, ['ID', 'Staff Name', 'Reference', 'Start Date', 'Days']);

// Fetch data from the database
$query = "SELECT id, name, reference, date FROM locker";
$result = $conn->query($query);

// Get current date
$current = date('d/m/Y', time());

while ($row = $result->fetch_assoc()) {
    $datetime = strtotime($row['date']);
    $startdate = date("d/m/Y", $datetime);
    
    // Calculate days difference
    $start = DateTime::createFromFormat('d/m/Y', $startdate);
    $end = DateTime::createFromFormat('d/m/Y', $current);
    $diff = $start->diff($end);
    $days_difference = $diff->days;

    // Hide date if it's 1970-01-01 or over 1000 days
    $startdate = ($startdate == "01/01/1970" || $days_difference > 1000) ? "" : $startdate;
    $days_difference = ($startdate == "" || $days_difference > 1000) ? "" : $days_difference;

    fputcsv($output, [$row['id'], $row['name'], $row['reference'], $startdate, $days_difference]);
}

// Close connection
$conn->close();
exit();
