---
layout: default
title: Project Setup
parent: Working with Platform.sh
nav_order: 1
---

# Project setup

Setting up a new project in Platform.sh is traditionally the role of Renewal SA's IT, however some notes are provided to validate that the project is set up correctly.

## Initial project configuration

When creating a new project, the following configuration is required.

|Platform SH Config Name  | Value  |
|--|--|
|Production environment name:  |  `live` |
|Region: | Australia (au-2) Azure |
|Organization: | Renewal SA Enterprise |

## Connect your git repository

All projects must use the `github` integration to link to a repository hosted in the Renewal SA Websites github organisation.

For ease of setup, it's required that this repo have `master`, `uat` and `live` branches.

## Domain configuration

Platform.sh suggests adding the platform domain as an "actual" domain on the project, as this can help in some scenarios when changing domains etc

In your Platform.sh project, go to **Settings**, then **Domains** for the `live` environment (https://console.platform.sh/renewal-sa/{$your-project-id}/live/settings/domains)

Add the default platform domain - format of `live-xxxxxx-${your-project-id}.platformsh.site` as a domain to the project.

This can also be complished via the CLI
```bash
# Add a domain to the live environment, replace the palceholders here with your values

platform domain:add live-xxxxxx-your-project-id.platformsh.site --project=your-project-id --environment=live

```

## Environments

In platform.sh the default setup is that `live` is the production environment, and the `uat` branch is also activated as an enviornment for testing.

If the project is setup as required above, then `uat` will be a child of `live`, and it will be easy to sync assets and DB from `live` to `uat`.

For larger projects on higher tiers of platform.sh application, there may be the possibility of activating more environments, developers are free to do this as they see fit.


