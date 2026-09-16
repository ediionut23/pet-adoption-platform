<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../../config/database.php';

$conn = getConnection();
$query = "SELECT id, name, species, breed, description FROM pets WHERE available_for_adoption = 1 ORDER BY id DESC FETCH FIRST 10 ROWS ONLY";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$pets = [];
while ($row = oci_fetch_assoc($stmt)) {
    $pets[] = $row;
}
oci_free_statement($stmt);
oci_close($conn);

$items = [];
foreach ($pets as $pet) {
    $items[] = [
        'title' => $pet['NAME'] . ' (' . $pet['BREED'] . ')',
        'link' => 'pet-page.php?id=' . $pet['ID'],
        'description' => $pet['DESCRIPTION'],
        'date' => null,
        'author' => null
    ];
}
echo json_encode($items); 