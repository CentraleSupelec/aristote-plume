FROM python:3.12-slim-bookworm AS python_base

RUN useradd -ms /bin/bash pyuser
WORKDIR /backend
COPY ./requirements.txt .

RUN apt-get update && \
    apt-get upgrade && \
    chown pyuser:pyuser requirements.txt && \
    su - pyuser -c "python -m pip install --upgrade pip" && \
    su - pyuser -c "pip install --no-cache-dir --upgrade -r /backend/requirements.txt" && \
    mkdir /backend/results_cache && \
    chown -R pyuser:pyuser /backend/results_cache/

COPY ./src src
COPY ./config config
COPY .env.dist .env.dist

COPY docker/backend-entrypoint.sh /backend-entrypoint.sh

ENTRYPOINT ["/bin/bash", "/backend-entrypoint.sh"]

USER pyuser

FROM python_base AS python_api

CMD ["python", "-m", "fastapi", "run", "/backend/src/api/main.py"]

FROM python_base AS python_worker

CMD ["python", "-m", "celery", "--app", "src.worker.main", "worker", "--loglevel=INFO", "--pool=threads"]
