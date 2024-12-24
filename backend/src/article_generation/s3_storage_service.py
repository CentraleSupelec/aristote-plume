import logging
import os

import boto3

logger = logging.getLogger(__name__)

class S3StorageService:
    def __init__(
        self,
        s3_storage_access_key: str,
        s3_storage_secret_key: str,
        s3_storage_endpoint_url: str,
        s3_storage_bucket_name: str,
        s3_storage_upload_directory: str,
    ):
        self.s3_storage_client = boto3.client(
            "s3",
            aws_access_key_id=s3_storage_access_key,
            aws_secret_access_key=s3_storage_secret_key,
            endpoint_url=s3_storage_endpoint_url
        )
        self.s3_storage_bucket_name = s3_storage_bucket_name
        self.s3_storage_upload_directory = s3_storage_upload_directory

    def upload_directory_to_s3(self, directory_path: str, prefix: str) -> bool:
        """
        Recursively upload a directory to an S3 bucket.

        :param directory_path: Path to the local directory to upload.
        :param prefix: Prefix to add to the S3 object keys.
        """
        abs_directory_path = os.path.abspath(directory_path)
        s3_prefix = os.path.join(self.s3_storage_upload_directory, prefix)
        upload_error = False

        for dirpath, dirnames, filenames in os.walk(abs_directory_path):
            for filename in filenames:
                local_path = os.path.join(dirpath, filename)
                relative_path = os.path.relpath(local_path, abs_directory_path)
                s3_key = os.path.join(s3_prefix, relative_path).replace("\\", "/")
                try:
                    logger.info(f"Uploading {local_path} to s3://{self.s3_storage_bucket_name}/{s3_key}...")
                    self.s3_storage_client.upload_file(local_path, self.s3_storage_bucket_name, s3_key)
                except Exception as e:
                    logger.error(f"Failed to upload {local_path}: {e}")
                    upload_error = True

        return upload_error

    @staticmethod
    def delete_local_directory_recursively(directory_path):
        for dirpath, dirnames, filenames in os.walk(directory_path, topdown=False):
            for file in filenames:
                os.remove(os.path.join(dirpath, file))
            for directory in dirnames:
                os.rmdir(os.path.join(dirpath, directory))
        os.rmdir(directory_path)
