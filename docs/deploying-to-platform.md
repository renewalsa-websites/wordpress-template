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

'https://{all}/':
    type: upstream
    upstream: "your-app-name:http"
# Add cache / other options as needed

"https://www.{all}/" :
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



### Environment & variables

## Deployment and builds

### First Deployment

### Testing on UAT
