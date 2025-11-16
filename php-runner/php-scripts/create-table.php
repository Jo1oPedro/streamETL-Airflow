<?php

// init_schema.php

$host   = getenv('DB_HOST') ?: 'postgres';
$port   = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'airflow';
$user   = getenv('DB_USER') ?: 'airflow';
$pass   = getenv('DB_PASS') ?: 'airflow';

$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "Conectado ao PostgreSQL em {$host}:{$port}/{$dbname}\n";

    $sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS bronze_events (
    id          SERIAL PRIMARY KEY,
    ts          TIMESTAMPTZ NOT NULL,
    value       INTEGER     NOT NULL,
    is_valid    BOOLEAN     NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS silver_events (
    id           SERIAL PRIMARY KEY,
    bronze_id    INTEGER NOT NULL UNIQUE REFERENCES bronze_events(id),
    ts_processed TIMESTAMPTZ NOT NULL,
    value        INTEGER NOT NULL,
    value2       INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS gold_metrics (
    id        SERIAL PRIMARY KEY,
    ts        TIMESTAMPTZ NOT NULL,
    avg_value NUMERIC(10, 2) NOT NULL
);
SQL;

    // Exec aceita múltiplos comandos separados por ';' no driver do Postgres
    $pdo->exec($sql);

    echo "Tabelas bronze_events, silver_events e gold_metrics criadas (ou já existiam).\n";

} catch (PDOException $e) {
    fwrite(STDERR, "Erro ao criar schema: " . $e->getMessage() . "\n");
    exit(1);
}
