---
layout: default
title: Configuration
parent: Working with Platform.sh
nav_order: 2
---

# Configuration

The WordPress starter template comes with example files for platform.sh configuration that should be suitable for most WordPress applications. Complex structures like WordPress multisite will require more thought, and the values within the various config files will need to be edited to match the requirements of the build and application.

## Important Files
`.platform.ap.yaml`
This is the main config file for platform, where the details of the build, mounts etc are all defined. This is also where the app's name / unique identifier is set.

`.environment`
This file is run after deploy and allows us to convert / export platform.sh provided environment variables into the variables our config is expecting. For custom variables (ie: those not provided under a different name by platform.sh) set them via the CLI or https://console.platform.sh UI.

`.platform/services.yaml`
This file defines other attached services that need to communicate with the main application container. The Starter template includes a MySQL DB service with 512MB of disk space allocated.

`.platform/routes.yaml`
This file defines the HTTP routing of the app, allowing for the accepted domains, and redirect rules, to be defined in code. The setup included in this template handles a single domain, and automatically adjusts to use the `default` domain from platform.sh.