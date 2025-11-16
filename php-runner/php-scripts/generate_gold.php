<?php

require __DIR__ . '/db.php';

$pdo = getPdo();

// Calcula média de value2 em silver
$avg = $pdo->query("SELECT AVG(value2) AS avg_value FROM silver_events")
    ->fetchColumn();

if ($avg === null) {
    $avg = 0;
}

$ts = (new DateTimeImmutable())->format(DATE_ATOM);

$stmt = $pdo->prepare("
    INSERT INTO gold_metrics (ts, avg_value)
    VALUES (:ts, :avg_value)
");

$stmt->execute([
    ':ts'        => $ts,
    ':avg_value' => $avg,
]);

echo "Gold metrics updated -> ts={$ts}, avg_value={$avg}\n";
