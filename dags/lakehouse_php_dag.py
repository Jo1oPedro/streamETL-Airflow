from airflow import DAG
from airflow.providers.docker.operators.docker import DockerOperator
from docker.types import Mount
from datetime import datetime, timedelta

default_args = {
    "owner": "airflow",
    "retries": 2,
    "retry_delay": timedelta(seconds=30),
}

with DAG(
    dag_id="lakehouse_php_pipeline",
    start_date=datetime(2024, 1, 1),
    schedule_interval="@daily",
    catchup=False,
    default_args=default_args
):

    common = dict(
        image="php-runner",
        auto_remove=True,
        docker_url="unix://var/run/docker.sock",
        mount_tmp_dir=False,
        network_mode="airflow_airflow_net",
        environment={
            "DB_HOST": "postgres",
            "DB_PORT": "5432",
            "DB_NAME": "airflow",
            "DB_USER": "airflow",
            "DB_PASS": "airflow",
        },
        )

    ingest_bronze = DockerOperator(
        task_id="ingest_bronze",
        command="php /src/php-scripts/produce_raw.php",
        **common,
    )

    validate_bronze = DockerOperator(
        task_id="validate_bronze",
        command="php /src/php-scripts/validate_raw.php",
        **common,
    )

    silver_transform = DockerOperator(
        task_id="silver_transform",
        command="php /src/php-scripts/transform_silver.php",
        **common,
    )

    gold_transform = DockerOperator(
        task_id="gold_transform",
        command="php /src/php-scripts/generate_gold.php",
        **common,
    )

    ingest_bronze >> validate_bronze >> silver_transform >> gold_transform