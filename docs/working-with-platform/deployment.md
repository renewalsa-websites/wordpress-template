---
layout: default
title: Deployment
parent: Working with Platform.sh
nav_order: 12
---

# Deployment

Platform.sh has steps to automatically build your application and deploy it to the correct environment. This is based on git branches. The `live` branch triggers a deploy to the live site, and the `uat` branch will trigger a deploy to this environment (once this environment is activated).

It is recommended to use the "live" environment as a staging site prior to the initial go-live. After a successful go-live, you can then activate the UAT environment, and sync the DB and Mounts (uploads etc) of the live site across.

## PHP dependencies and build
The build process will automatically call `composer install` as part of the build process, this will install any plugins and 3rd party libraries needed for your project.

Should you require other commands to be run as part of the deploy, this can be set via `.platform.app.yaml` - please review the [build hooks documentation](https://docs.platform.sh/create-apps/hooks.html) on Platform.sh and determine the [appropriate hook for your commands](https://docs.platform.sh/create-apps/hooks/hooks-comparison.html) depending on service availability and desired outcome.

## Javascript dependencies and build
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

## Simple .platform.app.yaml example

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
 
## First deployment

When first deploying the site you will need to upload the database and uploads to the platform.sh DB and mounted file system.

These actions are easily accomplushed via the CLI, pelase see the [databases]("/working-with-platform/databases") and [storage]("/working-with-platform/storage") docs for instructions on how to upload data and files.

## Going Live

When launching the site, the main responsibility will be changing the primary domain of the project. This can be done via the Console or CLI.

It is reccomended that you check over General Settings - `https://console.platform.sh/renewal-sa/your-project-id/live/settings` as well to ensure this is all as intended, ie: search engines allowed.
