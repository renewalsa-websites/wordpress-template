---
title: Deploying to Platform.sh
layout: default
nav_order: 6
---
# Deploying to platform.sh

Renewal SA websites are hosted on Platform.sh, a modern hosting platform that combines  dev-ops with high-end application hosting. Platform.sh is built to work in tandem with git, and has numerous features to help work across multiple environments for each site.

Using Platform.sh imposes some limitations or requirements on our apps - but these align with best practices and the [12 Factor App](https://12factor.net) guidelines for building solid modern applications.

To best understand Platform.sh it's recommended that you  read their own documentation

 - [Getting Started](https://docs.platform.sh/learn/overview.html)
 - [Philosophy](https://docs.platform.sh/learn/overview/philosophy.html)
 - [Structure](https://docs.platform.sh/learn/overview/structure.html)
 - [Build and deploy](https://docs.platform.sh/learn/overview/build-deploy.html)

## Project Structure

The WordPress starter template comes with example files for platform.sh configuration that should be suitable for most WordPress applications. Complex structures like WordPress multisite will require more thought, and the values within the various config files will need to be edited to match the requirements of the build and application.

**Notable files**
`.platform.ap.yaml`
This is the main config file for platform, where the details of the build, mounts etc are all defined. This is also where the app's name / unique identifier is set.

`.environment`
This file is run after deploy and allows us to convert / export platform.sh provided environment variables into the variables our config is expecting. For custom variables (ie: those not provided under a different name by platform.sh) set them via the CLI or https://console.platform.sh UI.

`.platform/services.yaml`
This file defines other attached services that need to communicate with the main application container. The Starter template includes a MySQL DB service with 512MB of disk space allocated.

`.platform/routes.yaml`
This file defines the HTTP routing of the app, allowing for the accepted domains, and redirect rules, to be defined in code. The setup included in this template handles a single domain, and automatically adjusts to use the `default` domain from platform.sh.

### Services

Additional Services that should be connected to the main PHP application. The most common use case of this is a database (MySQL). If you needed to add an object cache like Redis then this would also be provisioned as a service and attached to the main PHP application.

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
{: .note }
Please be aware that the disk size of the DB counts towards the total disk usage for the project. Projects will fail to deploy if the **combined** requested disk size is over the total allowance for the project.

### Routing
The routes file defines how to route incoming requests to services, along with redirects, accepted domains, and other information.

Most projects can utilise the default `.platform/routes.yaml` included in the project. This will route the 'default' domain to the upstream application, and redirect the `www` version of that domain to the root domain.

The default `.platform/routes.yaml`
```yaml
# The routes of the project.
#
# Each route describes how an incoming URL is going
# to be processed by Platform.sh.

"https://{default}/":
    type: upstream
    upstream: "your-app-name:http"
    cache:
        # Set to true when ready to go live
        enabled: false
        # Base the cache on the session cookies. Ignore all other cookies.
        cookies:
            - '/^wordpress_logged_in_/'
            - '/^wordpress_sec_/'
            - 'wordpress_test_cookie'
            - '/^wp-settings-/'
            - '/^wp-postpass/'
            - '/^wp-resetpass-/'

"https://www.{default}/":
    type: redirect
    to: "https://{default}/"
```
{: .highlight }
Be sure to update the `upstream` property in the routes file to reference your unique app name, as defined in `.platform.app.yaml`. 

#### Multisite Environments
To handle multisite environments, the default configuration will need to be altered. There are two options depending on the circumstances

 - Use the `{all}` directive
 - Specify each domain individually

All directive example
```yaml
# The routes of the project.
#
# Each route describes how an incoming URL is going
# to be processed by Platform.sh.

"https://{all}/":
    type: upstream
    upstream: "your-app-name:http"
# Add cache / other options as needed

"https://www.{all}/":
    type: redirect
    to: "https://{all}/"
```

Individual domain specified example (repeat as needed)
```yaml
# The routes of the project.
#
# Each route describes how an incoming URL is going
# to be processed by Platform.sh.

"https://domain1.com.au/":
    type: upstream
    id: "primary"
    upstream: "your-app-name:http"

"https://domain2.com.au/":
    type: upstream
    id: "secondary"
    upstream: "your-app-name:http"

"https://www.domain1.com.au/":
    type: redirect
    to: https://domain1.com.au/

"https://www.domain2.com.au/":
    type: redirect
    to: https://domain2.com.au/
```

The WordPress template is already set up to work with multiple incoming URLs, using `$_SERVER['HTTP_HOST']` as the `WP_HOME` / `WP_SITEURL` constants. 

### Storage & mounts

The 'standard' filesytem on Platform.sh is ready-only by default. Any directories or files that you wish to be writable must exist within pre-defined "mounts". These are persisted between deployments, getting detached and re-attached as new containers are created for each deployment.

Platform.sh also provides a simple way to synchronise these files across environments, making it trivial to update the `uat` environment with the uploads and DB from `live`.

For WordPress the main requirement is a writable directory for uploads.

Mounts and storage size are defined in `.platform.app.yaml`

Storage size standard definition - 
```yaml
# The size of the persistent disk of the application (in MB). Minimum value is 128.
# Default projects have 5GB of storage, we allocate 4608MB to mounts / uploads, and 512 to DB disk.
disk: 4608
```

{: .warning }
When setting the disk size, be sure to account for the DB disk size as well (as defined in `.platform/services.yaml`). The total size of the Mounts + DB must be equal to or less than the allocated project storage to successfully deploy.


Mounts standard definition - 
```yaml
# The following block defines a writable directory, 'site/content/uploads'
# The 'source' specifies where the writable mount is. The 'local' source
# indicates that the mount point will point to a local directory on the
# application container. The 'source_path' specifies the subdirectory
# from within the source that the mount should point at. 
mounts:
    'site/content/cache':
        source: local
        source_path: 'cache'
    'site/content/uploads':
        source: local
        source_path: 'uploads'
```

If your application requires other writable directories then they can be added to the above list - in the case of WordPress this is generally discouraged, as all "stateful" content should be in `uploads`.

### Environment & variables
This section discusses "environment" and "variables" from the context of your applications runtime environment. Many modern PHP applications use something like `.env` to load local config into environment variables.

This WordPress project is no different - it uses `.env` for local development, but your `.env` file should never be committed, as it is not needed (and will have incorrect details) in production.

When deploying to platform.sh enviornments, we use the `.environment` file to map the platform.sh supplied variables to the variables that the `site/wp-config.php` file is expecting.

Variables can also be set via CLI or the GUI console in Platform.sh, and this can be used for things like AWS keys, API keys, License keys etc.

To learn more, please consult the [platform.sh documentation](https://docs.platform.sh/development/variables/set-variables.html).

Here is the `.environment` file included in the project. This file uses bash syntax, and is run whenever a new container / environment is started.  You can see examples below of how pre-existing platform.sh environment variables are read, processed, and then populated into the environment variables names that WordPress is looking for (via our `site/wp-config.php` file).

```bash
# .environment
export WP_DB_NAME=$(echo $PLATFORM_RELATIONSHIPS | base64 --decode | jq -r ".database[0].path")
export DB_HOST=$(echo $PLATFORM_RELATIONSHIPS | base64 --decode | jq -r ".database[0].host")
export DB_PORT=$(echo $PLATFORM_RELATIONSHIPS | base64 --decode | jq -r ".database[0].port")
export DB_USER=$(echo $PLATFORM_RELATIONSHIPS | base64 --decode | jq -r ".database[0].username")
export DB_PASSWORD=$(echo $PLATFORM_RELATIONSHIPS | base64 --decode | jq -r ".database[0].password")

export WP_HOME=$(echo $PLATFORM_ROUTES | base64 --decode | jq -r 'to_entries[] | select(.value.primary == true) | .key')

export WP_DEBUG_LOG=/var/log/app.log
if [ "$PLATFORM_BRANCH" != "live" ] ; then
   export WP_ENV='development'
else
   export WP_ENV='production'
fi

export AUTH_KEY=$PLATFORM_PROJECT_ENTROPY
export SECURE_AUTH_KEY=$PLATFORM_PROJECT_ENTROPY
export LOGGED_IN_KEY=$PLATFORM_PROJECT_ENTROPY
export NONCE_KEY=$PLATFORM_PROJECT_ENTROPY
export AUTH_SALT=$PLATFORM_PROJECT_ENTROPY
export SECURE_AUTH_SALT=$PLATFORM_PROJECT_ENTROPY
export LOGGED_IN_SALT=$PLATFORM_PROJECT_ENTROPY
export NONCE_SALT=$PLATFORM_PROJECT_ENTROPY
```

Please note that it's not good practice to hardcode values (such as license keys etc) into `.environment`, do not paste sensitive credentials into this file as it is committed to git.

## Deployment and builds

Platform.sh has steps to automatically build your application and deploy it to the correct environment. This is based on git branches. The `live` branch triggers a deploy to the live site, and the `uat` branch will trigger a deploy to this environment (once this environment is activated).

It is recommended to use the "live" environment as a staging site prior to the initial go-live. After a successful go-live, you can then activate the UAT environment, and sync the DB and Mounts (uploads etc) of the live site across.

### PHP dependencies and build
The build process will automatically call `composer install` as part of the build process, this will install any plugins and 3rd party libraries needed for your project.

Should you require other commands to be run as part of the deploy, this can be set via `.platform.app.yaml` - please review the [build hooks documentation](https://docs.platform.sh/create-apps/hooks.html) on Platform.sh and determine the [appropriate hook for your commands](https://docs.platform.sh/create-apps/hooks/hooks-comparison.html) depending on service availability and desired outcome.

### Javascript dependencies and build
This project also includes a build script to install javascript as defined in `/package.json` and run a `build` command. It does so using Yarn, though is easy enough to edit to use NPM and whatever your build command is.

{: .note }
The default build script assumes your `package.json` is in the project's root folder. Some agencies will have this file within the WordPress theme. Alter the paths of the build script as necessary, it is suggested that the `node_modules` directory be deleted after a successful build to avoid taking up too much disk space.

The included build example is as follows 
 
 ```yaml
 variables:
    env:
        # Update for your desired NVM version.
        NVM_VERSION: v0.37.2
        NODE_VERSION: v18.14.2

# Other config.......#

# Installs global dependencies as part of the build process. They’re independent of your app’s dependencies and
# are available in the PATH during the build process and in the runtime environment. They’re installed before
# the build hook runs using a package manager for the language.
# More information: https://docs.platform.sh/create-apps/app-reference.html#dependencies
dependencies:
    php:
        composer/composer: '^2'
        wp-cli/wp-cli-bundle: "^2.4.0"
    nodejs:
        yarn: "1.22.19"

# Hooks allow you to customize your code/environment as the project moves through the build and deploy stages
# More info:
hooks:
    # The build hook is run after any build flavor.
    # More information: https://docs.platform.sh/create-apps/hooks/hooks-comparison.html#build-hook
    build: |
        set -e
        unset NPM_CONFIG_PREFIX
        export NVM_DIR="$PLATFORM_APP_DIR/.nvm"

        # Link cache with app
        if [ ! -d "$PLATFORM_CACHE_DIR/.nvm" ]; then
            mkdir -p $PLATFORM_CACHE_DIR/.nvm
        fi
        ln -s $PLATFORM_CACHE_DIR/.nvm $NVM_DIR

        # Check for Node.js version and install if not present
        if [ ! -d "$PLATFORM_CACHE_DIR/.nvm/versions/node/$NODE_VERSION" ]; then

            # Get nvm install script if correct version not present
            export NVM_INSTALL_FILE="${PLATFORM_CACHE_DIR}/nvm_${NVM_VERSION}_install.sh"
            if [ ! -f "$NVM_INSTALL_FILE" ]; then
                wget -nc -O "$NVM_INSTALL_FILE" "https://raw.githubusercontent.com/nvm-sh/nvm/$NVM_VERSION/install.sh"
            fi

            # Install, automatically using NODE_VERSION 
            bash $NVM_INSTALL_FILE
        fi

        # Activate nvm
        [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

        # Use the specified version
        nvm use "$NODE_VERSION"
        # List the current directory (for debugging in the logs)
        ls -al
        # Install dependencies (respecting lockfile)
        yarn --frozen-lockfile 
        # Build command as defined in package.json
        yarn build
 ```
**Simple .platform.app.yaml example**

If you are creating a WordPress site that does not require a front-end build step (no NPM, JS compilation, Sass etc) then the following example shows a stripped back `.platform.app.yaml` without all the build steps.

```yaml
# Complete list of all available properties: https://docs.platform.sh/create-apps/app-reference.html


# A unique name for the app. Must be lowercase alphanumeric characters. Changing the name destroys data associated
# with the app.
name: your-app-name

# The runtime the application uses.
# Complete list of available runtimes: https://docs.platform.sh/create-apps/app-reference.html#types
type: 'php:8.1'

# Specifies a default set of build tasks to run. Flavors are language-specific.
# More information: https://docs.platform.sh/create-apps/app-reference.html#build
build:
    flavor: composer

# Installs global dependencies as part of the build process. They’re independent of your app’s dependencies and
# are available in the PATH during the build process and in the runtime environment. They’re installed before
# the build hook runs using a package manager for the language.
# More information: https://docs.platform.sh/create-apps/app-reference.html#dependencies
dependencies:
    php:
        composer/composer: '^2'
        wp-cli/wp-cli-bundle: "^2.4.0"

# The relationships of the application with services or other applications.
# The left-hand side is the name of the relationship as it will be exposed
# to the application in the PLATFORM_RELATIONSHIPS variable. The right-hand
# side is in the form `<service name>:<endpoint name>`.
# More information: https://docs.platform.sh/create-apps/app-reference.html#relationships
relationships:
    database: "db:mysql"

# The web key configures the web server running in front of your app.
# More information: https://docs.platform.sh/create-apps/app-reference.html#web
web:
    # Each key in locations is a path on your site with a leading /.
    # More information: https://docs.platform.sh/create-apps/app-reference.html#locations
    locations: 
        "/":
            # The public directory of the app, relative to its root.
            root: "site"
            # The front-controller script to send non-static requests to.
            passthru: "/index.php"
            # Wordpress has multiple roots (wp-admin) so the following is required
            index:
                - "index.php"
            # The number of seconds whitelisted (static) content should be cached.
            expires: 600
            scripts: true
            allow: true
            rules:
                ^/composer\.json:
                    allow: false
                ^/license\.txt$:
                    allow: false
                ^/readme\.html$:
                    allow: false
        "/content/cache":
            root: "site/content/cache"
            scripts: false
            allow: false
        "/content/uploads":
            root: "site/content/uploads"
            scripts: false
            allow: false
            rules:
                # Allow access to common static files.
                '(?<!\-lock)\.(?i:jpe?g|gif|png|webp|svg|bmp|ico|css|js(?:on)?|eot|ttf|woff|woff2|pdf|zip|docx?|xlsx?|pp[st]x?|psd|odt|key|mp[2-5g]|m4[av]|og[gv]|wav|mov|wm[av]|avi|3g[p2])$':
                    allow: true
                    expires: 1w

# The size of the persistent disk of the application (in MB). Minimum value is 128.
disk: 4608

# The following block defines a single writable directory, 'web/uploads'
# The 'source' specifies where the writable mount is. The 'local' source
# indicates that the mount point will point to a local directory on the
# application container. The 'source_path' specifies the subdirectory
# from within the source that the mount should point at. 
mounts:
    'site/content/cache':
        source: local
        source_path: 'cache'
    'site/content/uploads':
        source: local
        source_path: 'uploads'
```
 
### First deployment and syncing content

When first deploying the site you will need to upload the database and uploads to the platform.sh DB and mounted file system. The Platform.sh CLI provides helper commands to make this easy.

{: .note }
This process is easier if done from your local machine due to the way authentication works. If necessary just `rsync` the data to your local from staging before using the local copy to push to platform.sh.

#### Working with databases

To upload the database, use the following command to ingest a SQL file.

```bash
# Upload the database.sql to the live environment of the project
platform db:sql --environment=live --project=yourprojectid < path/to/database.sql
```
{:.note}
You can omit the `--project` and `--environment` flags if you don't have them handy - the platform.sh CLI will run in interactive mode allowing you to choose a project and environment.

You can also easily dump the DB to your local machine for easy development
```bash
# Dump the live DB to a file called 'live-database.sql'
platform db:dump --environment=live --project=yourprojectid --filename="live-database.sql"

# Stream the dump of the live DB directly into your local WP Database using WP CLI
# Presumes WP CLI set up and configured locally
platform db:dump --environment=live --project=yourprojectid --stdout | wp db import -
```

Platform.sh has a limited [command reference for these commands here](https://docs.platform.sh/administration/cli/reference.html#dbdump), but it is recommended just to use the `help` function - `platform help db:dump` etc.

#### Working with mounted files (uploads folder)

Similar the DB, it is common to have to upload the 'stateful' files of a WordPress installation, particularly when deploying for the first time or getting ready to go live.

Mounts are defined in `platform.app.yaml`, however you can always list out the active mounts for a project using the CLI.

```bash
platform mount:list --project=yourprojectid --environment=live
```

Take note of the "Mount path", as this will be used when we upload the files.

To upload our files, platform.sh uses the `mount:upload` command, this uses `rsync` under the hood to ensure a fast, safe and predictable outcome when uploading the assets. The command takes two main flags `--source` for the path to the files locally, and `--mount` for the path to the mounted filesystem in your app.

{:.warning}
As we are dealing with paths here, be sure to adjust and correct the paths for your project. Do not use trailing slashes on the paths for --source and --mount, as this can have unintended effects.

Example: 
```bash
# This example presumes you are running the default directory structure of this project as
# specified in this repo. 

# The `--source` path is relative and the command presumes you are in the root directory  
# of the project.

platform mount:upload --mount site/content/uploads --source site/content/uploads --project=yourprojectid --environment=live
```


### Testing on UAT
