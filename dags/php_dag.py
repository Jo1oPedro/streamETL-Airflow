from airflow import DAG
from airflow.providers.docker.operators.docker import DockerOperator
from datetime import datetime, timedelta

default_args = {
    "owner": "airflow",
    "retries": 1,
    "retry_delay": timedelta(minutes=1),
}

with DAG(
    dag_id="rodar_script_php",
    default_args=default_args,
    description="Executa script PHP usando DockerOperator",
    schedule_interval="@daily",
    start_date=datetime(2024, 1, 1),
    catchup=False,
) as dag:

    tarefa_php = DockerOperator(
        task_id="executar_php",
        image="php-runner:latest",  # imagem criada no docker-compose
        container_name="run-php",
        api_version="auto",
        auto_remove=True,
        command="php /src/script.php",
        docker_url="unix://var/run/docker.sock",
        mount_tmp_dir=False,
        network_mode="bridge",
    )
