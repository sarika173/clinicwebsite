<?php
include 'db.php';

// Fetch all medicines
$query = "SELECT * FROM inventory";
$result = mysqli_query($conn, $query);

// Display medicines in a table
echo '<table class="table table-bordered">';
echo '<thead>';
echo '<tr>';
echo '<th>Name</th>';
echo '<th>Quantity</th>';
echo '<th>Expiry Date</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr class="' . ($row['quantity'] < 5 ? 'low-stock' : '') . '">';
    echo '<td>' . $row['medicine_name'] . '</td>';
    echo '<td>' . $row['quantity'] . '</td>';
    echo '<td>' . $row['expiry_date'] . '</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
?>
