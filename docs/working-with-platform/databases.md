---
layout: default
title: Databases (services)
parent: Working with Platform.sh
nav_order: 8
---


# Databases (and services)

The database is a vital part of our Wordpress application, and the DB is referred to as a 'service' in platform.sh. Services are additional process (isolated via containers) that should be connected to the main PHP application during runtime. 

The most common use case of this is a plain database (MySQL), however if you needed to add an object cache like Redis then this would also be provisioned as a service and attached to the main PHP application.

Services are defined in `.platform/services.yaml`

```yaml
# The name given to the MariaDB/MySQL service (lowercase alphanumeric only).
db:
    # The type of your service (mysql), which uses the format
    # 'type:version'. Be sure to consult the MariaDB/MySQL documentation
    # (https://docs.platform.sh/add-services/mysql.html#supported-versions)
    # when choosing a version. If you specify a version number which is not available,
    # the CLI will return an error.
    type: mysql:11.0
    # The disk attribute is the size of the persistent disk (in MB) allocated to the service.
    disk: 512
```

If you need to increase the size of the DB disk, it can be done via this file.
{: .info}
Please be aware that the disk size of the DB counts towards the total disk usage for the project. Projects will fail to deploy if the **combined** requested disk size is over the total allowance for the project.

## Accessing databases and services

Each environment will have their own instance of the associated app's services. As services are meant to cary stateful data, it is natural that the services have seperate instances to accomodate for different data in different environments.

Because of this stateful data, there is often a need to pull down this data to help with local development. In limited cases, there is also a need to push new data up to these services, and both of these can be accomplished easily using the platform.sh CLI.

{: .warning }
If you find yourself repeatedly needing to push data to the live DB after the initial launch, this is generally a sign to work on your process. The platform.sh environment is built around a "Code up, data down" workflow.

### Downloading the database

You can also easily dump the DB to your local machine for easy development
```bash
# Dump the live DB to a file called 'live-database.sql'
platform db:dump --environment=live --project=yourprojectid --filename="live-database.sql"

# Stream the dump of the live DB directly into your local WP Database using WP CLI
# Presumes WP CLI set up and configured locally
platform db:dump --environment=live --project=yourprojectid --stdout | wp db import -
```

### Uploading the database
To upload the database, use the following command to ingest a SQL file.

```bash
# Upload the database.sql to the live environment of the project
platform db:sql --environment=live --project=yourprojectid < path/to/database.sql
```
{:.note}
You can omit the `--project` and `--environment` flags if you don't have them handy - the platform.sh CLI will run in interactive mode allowing you to choose a project and environment.


## Additional information

Some additional resources to help with diving deeper.

[Platform.sh structure documentation](https://docs.platform.sh/learn/overview/structure.html)

[Platform.sh services reference](https://docs.platform.sh/add-services.html)

Platform.sh has a limited [command reference for these commands here](https://docs.platform.sh/administration/cli/reference.html#dbdump), but it is recommended just to use the `help` function - `platform help db:dump` etc.