---
layout: default
title: Environment
parent: Working with Platform.sh
nav_order: 4
---

# Environment
This section discusses "environment" and "variables" from the context of your applications runtime environment.

Many modern PHP applications use something like `.env` to load local config into environment variables.

This WordPress project is no different - it uses `.env` for local development, but your `.env` file should never be committed, as it is not needed (and will have incorrect details) in production.

When deploying to platform.sh enviornments, we use the `.environment` file to map the platform.sh supplied variables to the variables that the `site/wp-config.php` file is expecting.

Variables can also be set via CLI or the GUI console in Platform.sh, and this can be used for things like AWS keys, API keys, License keys etc.

To learn more, please consult the [platform.sh documentation](https://docs.platform.sh/development/variables/set-variables.html).

## Sample `.environment` file

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

