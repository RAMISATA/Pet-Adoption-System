<?php
// Connect to Oracle
$conn = oci_connect('system', '123', 'localhost/XE'); // Change these with your credentials

if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . htmlentities($e['message'], ENT_QUOTES));
}

// Get the SQL query from the form input
$query = $_GET['query'] ?? '';

// Simple security: allow only SELECT queries
if (!preg_match('/^\s*select/i', $query)) {
    die("Only SELECT queries are allowed.");
}

// Prepare and execute the query
$stid = oci_parse($conn, $query);

if (!oci_execute($stid)) {
    $e = oci_error($stid);
    die("Query failed: " . htmlentities($e['message'], ENT_QUOTES));
}

// Display results in a table
echo "<h2>Search Results</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";

// Table header
echo "<tr>";
for ($i = 1; $i <= oci_num_fields($stid); $i++) {
    $colName = oci_field_name($stid, $i);
    echo "<th>" . htmlspecialchars($colName) . "</th>";
}
echo "</tr>";

// Table rows
while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
    echo "<tr>";
    foreach ($row as $item) {
        echo "<td>" . htmlspecialchars($item ?? '') . "</td>";
    }
    echo "</tr>";
}

echo "</table>";

// Clean up
oci_free_statement($stid);
oci_close($conn);
?>