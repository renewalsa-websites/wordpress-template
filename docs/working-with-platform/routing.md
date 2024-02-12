---
layout: default
title: Routing
parent: Working with Platform.sh
nav_order: 6
---

# Routing
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

## Multisite Environments
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