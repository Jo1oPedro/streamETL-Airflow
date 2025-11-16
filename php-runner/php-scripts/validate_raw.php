<?php

require __DIR__ . '/db.php';

$pdo = getPdo();

// Regra de validação: ts/value não nulos e value > 0
$sql = "
    UPDATE bronze_events
    SET is_valid = FALSE
    WHERE is_valid = TRUE
      AND (ts IS NULL OR value IS NULL OR value <= 0)
";

$updated = $pdo->exec($sql);

echo "Marked {$updated} bronze events as invalid\n";
