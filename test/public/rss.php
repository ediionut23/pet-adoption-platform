<?php
header("Content-Type: application/rss+xml; charset=UTF-8");
require_once __DIR__ . '/../config/database.php';

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

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0">
<channel>
    <title>Oferte adopție animale</title>
    <link>https://siteultau.ro/</link>
    <description>Ultimele animale propuse spre adopție</description>
    <?php foreach ($pets as $pet): ?>
    <item>
        <title><?= htmlspecialchars($pet['NAME']) ?> (<?= htmlspecialchars($pet['BREED']) ?>)</title>
        <link>pet-page.php?id=<?= $pet['ID'] ?></link>
        <description><?= htmlspecialchars($pet['DESCRIPTION']) ?></description>
        <guid>pet-page.php?id=<?= $pet['ID'] ?></guid>
    </item>
    <?php endforeach; ?>
</channel>
</rss> 