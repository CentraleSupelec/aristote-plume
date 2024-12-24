import os.path
from functools import lru_cache

from pydantic_settings import BaseSettings, SettingsConfigDict

def resolve_plume_env() -> str:
    if os.path.exists(".env"):
        return ".env"
    elif os.path.exists(".env.dist"):
        return ".env.dist"
    raise FileNotFoundError("No env file was found (tried '.env' and 'env.dist').")

class Settings(BaseSettings):
    redis_broker_dsn: str = "redis://:redis_password@localhost:6379/2"
    redis_backend_dsn: str = "redis://:redis_password@localhost:6379/3"
    aristote_dispatcher_uri: str = "https://dispatcher.aristote.centralesupelec.fr"
    aristote_dispatcher_port: int = 443
    aristote_dispatcher_api_key: str = "change_me_in_env_file"
    s3_storage_endpoint_url: str = "change_me_in_env_file"
    s3_storage_upload_directory: str = "change_me_in_env_file"
    s3_storage_bucket_name: str = "change_me_in_env_file"
    s3_storage_access_key: str = "change_me_in_env_file"
    s3_storage_secret_key: str = "change_me_in_env_file"

    model_config = SettingsConfigDict(env_file=resolve_plume_env())

@lru_cache
def get_settings() -> Settings:
    return Settings()
