<?php

require __DIR__ . '/db.php';

$pdo = getPdo();

// Pega bronze válidos que AINDA NÃO foram processados para silver
$sql = "
    SELECT b.id, b.ts, b.value
    FROM bronze_events b
    LEFT JOIN silver_events s ON s.bronze_id = b.id
    WHERE b.is_valid = TRUE
      AND s.id IS NULL
    ORDER BY b.id
";

$stmt = $pdo->query($sql);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    echo "No new bronze events to process\n";
    exit(0);
}

$insert = $pdo->prepare("
    INSERT INTO silver_events (bronze_id, ts_processed, value, value2)
    VALUES (:bronze_id, :ts_processed, :value, :value2)
");

$processed = 0;

$now = (new DateTimeImmutable())->format(DATE_ATOM);

foreach ($rows as $row) {
    $value2 = $row['value'] * 2;

    $insert->execute([
        ':bronze_id'    => $row['id'],
        ':ts_processed' => $now,
        ':value'        => $row['value'],
        ':value2'       => $value2,
    ]);

    $processed++;
}

echo "Processed {$processed} bronze events into silver\n";
