<?php

require __DIR__ . '/db.php';

$pdo = getPdo();

$ts = (new DateTimeImmutable())->format(DATE_ATOM); // ISO 8601
$value = random_int(1, 999);

$stmt = $pdo->prepare("
    INSERT INTO bronze_events (ts, value)
    VALUES (:ts, :value)
");

$stmt->execute([
    ':ts'    => $ts,
    ':value' => $value,
]);

echo "Generated raw event -> ts={$ts}, value={$value}\n";
