# Plume (Aristote)

This technical README should help you set up a local development environment for the Aristote Plume project.

## Prerequisites

* Git
* Docker and docker compose

## Web application

### Quick start

Run the following command to start the project :

```shell
ln -s ../../webapp/bin/pre-commit.sh .git/hooks/pre-commit # pre-commit script (to run php-cs-fixer)
cp -f ./webapp/.env ./webapp/.env.local
cp -f .php-cs-fixer.dist.php .php-cs-fixer.php
echo "127.0.0.1 aristote-plume-local.centralesupelec.fr" | sudo tee -a /etc/hosts
docker compose up --build -d
```

Make sure to adjust the `.env.local` file to your needs. Some values are just placeholders for secrets, or will depend
on your configuration. Take a look at the [application configuration](#application-configuration) section of the readme.

* Once up and running, the application is accessible at the following urls :
    * Web application homepage : https://aristote-plume-local.centralesupelec.fr/
    * Web application administration area : https://aristote-plume-local.centralesupelec.fr/admin

To access authenticated areas of the application, you need to configure the authentication provider, and create your
first administrator user.

### Users authentication: OIDC

Users authentication is delegated to an external provider with
[OpenID Connect protocol](https://openid.net/developers/how-connect-works/). The following environment variables must be
configured:

```dotenv
OIDC_WELL_KNOWN_URL="Your OIDC server well known URL"
OIDC_CLIENT_ID="The client ID"
OIDC_CLIENT_SECRET="The client secret"
OIDC_SCOPES="OIDC scopes as comma separated values (e.g. 'openid' or 'openid,profile,email')"
OIDC_USER_IDENTIFIER_PROPERTY="Must be the user email"
OIDC_USER_IDENTIFIER_FROM_ID_TOKEN="Whether email should be obtained from the userinfo endpoint (0) or id token (1)"
```

If you do not have an OIDC provider to configure, or if it is not possible to set it up for your development
environment, you can set up a
[keycloak OIDC server](https://www.keycloak.org/docs/latest/server_admin/#con-oidc_server_administration_guide) locally.

[//]: # (TODO: add more documentation on how to setup keycloak locally + add keycloak to docker compose file.)

### Object storage: minio

In the local development environment, [minio](https://min.io/docs/minio/container/index.html) is used to provide object
storage with s3 apis compatibility. To configure `minio`:

1. Go to the [minio admin dashboard](http://localhost:9091), and log in as administrator (root credentials in
   environment variables of the `minio` service in [compose.yml](compose.yml))
2. Create a bucket for the application (`plume-local`)
3. Create an access key, and report its identifier / secret in the corresponding environment variables
   (`OVH_ACCESS_KEY` / `OVH_SECRET_KEY`)

The following environment variables can now be configured:

```dotenv
# Virtual host style is used by the php aws s3 client
OVH_BASE_URL=http://staffing-local.minio:9000
OVH_ENDPOINT_URL=http://minio:9000
OVH_UPLOAD_DIRECTORY=dev
# OVH_REGION is ignored in development environment
OVH_REGION=eu-west-2
OVH_BUCKET_NAME=staffing-local
OVH_ACCESS_KEY=your_access_key_id
OVH_SECRET_KEY=your_access_key_secret
```

This configuration should not be used as-is in a deployed environment (minio should be configured more finely).

## Python API and workers

[//]: # (TODO: write docs on how to configure & start)
