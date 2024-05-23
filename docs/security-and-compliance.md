---
title: Security and compliance
layout: default
nav_order: 5
---
# Security and Compliance

## Compliance
This documentation offers some helpful practical tips for complying with the standards, but first and foremost the point of truth for all compliance and standards are the SA Government's own documentation. 

At the time of writing these are summarised in the [SACSF-S4-16-Secure-Web-Service-Standard.pdf](/assets/SACSF-S4-16-Secure-Web-Service-Standard.pdf) document and supporting documents.

Government websites are required to be accessible - with sites complying with either [WCAG 2.0](https://www.w3.org/TR/WCAG20/) at Level A or Level AA.

Performance is also a form of accessibility - slow sites with huge bandwidth requirements discriminate against people with older devices or slower internet connections. All sites should score 85 or higher in [Google Lighthouse](https://developer.chrome.com/docs/lighthouse/overview) for both Mobile or Desktop.

### Security 
To help meet the security requirements this project comes with some plugins in the `composer.json`. 

 - WP 2FA for Two Factor Authentication (mandatory for all admins)
 - WordPress Password bCrypt to ensure srong hashing of passwords
 - Disable Comments to turn off comment functionality and reduce possible attack vectors
 - WP Security Audit Log Premium to log user activity within the site
 - Limit Logins Attempt (reloaded) to prevent brute force attacks against user passwords

These plugins must be activated and configured in order to work properly.

 - WP Security Audit Log should be setup so that only one user can see / clear the log
 - Disable Comments should be set to turn comments off for everything
 - WP 2FA must be configured to require 2FA for all Admin users, it is recommended for editor users as well or any user level that can access Personally Identifiable Information such as form submissions.

For security reasons, and to work with Platform.SH, some parts of the WordPress admin are disabled
- The theme editor is disabled
- The plugin editor is disabled
- Any plugin that allows the user to edit the file system via the admin is banned
- The installation of plugins via the admin is banned

### Change management and version control
The use of version control is mandatory for the development of websites, and our partners are encourage to pick approaches that promote a separation of concerns between structure and content, with structure being managed and tracked via code, and content is kept in the database.

Some practical examples of this include

 - Using ACF (with field definitions tracked in `acf-json` or defined in code) vs a Page Builder
 - Define custom posts and taxonomies in code vs via a plugin
 - Layouts and Styling being defined via the theme template files and css vs a Page Builder