FROM python:3.12-slim-bookworm as python_base

WORKDIR /backend
COPY ./requirements.txt requirements.txt

RUN pip install --no-cache-dir --upgrade -r requirements.txt

COPY ./src src
COPY ./config config
COPY .env.dist .env.dist

COPY docker/backend-entrypoint.sh /backend-entrypoint.sh

ENTRYPOINT ["/bin/bash", "/backend-entrypoint.sh"]

FROM python_base as python_api

CMD ["fastapi", "run", "/backend/src/api/main.py"]

FROM python_base as python_worker

CMD ["celery", "--app", "src.worker.main", "worker", "--loglevel=INFO", "--pool=threads"]
