---
title: Patching and maintenance
layout: default
nav_order: 6
---
# Patching and maintenance

Maintaining the security and compliance of an application is an ongoing process, which is expected to involve regular updates and patches in response to new version being published and vulnerabilities being discovered and mitigated within the application's core and dependencies such as plugins.

Whether the application is being maintained by the original developers, or another party, the following standards should be adhered to when performing regular updates. Please consider the following requirements when quoting and proposing a maintenance plan for your Renewal SA project.

## General Updates and Patching

Maintenance and upgrades should be performed fortnightly. All dependencies and libraries should be upgraded to the most recent possible *minor versions*  as defined by SemVar.

- Major version upgrades should also be attempted if possible, but these may require more planning, additional remediation for breaking changes to the application etc 
- In a case where there is a mix of Major and Minor versions to upgrade to, it is the minimum requirement that the Minor versions upgrades take place, with the Major version upgrades flagged for later review. 
- The deployment of Minor version updates should not be delayed by remediation work required for a Major version update.

This base requirement acts as the *general rule* for upgrades and patching for applications, but is superseded in certain cases relating to known CVEs or vulnerabilities.

## Published CVEs and security vulnerabilities

When a component of the application has a published vulnerability, this should be patched and addressed within 48hrs. It is expected that in almost all cases, these patches will come from the vendor of the library / CMS / dependency. 

If a package is unmaintained, then the maintenance provider should suggest a way forward. This may involve replacing it with a maintained and secure dependency, or to patch the existing dependency by maintaining a fork or contributing upstream if possible, and outline any costs associated with this. 

Such events are rare, and are typically considered outside the scope of the normal maintenance agreement, though this varies by supplier - any contractual agreements supersedes the general advice and assumptions provided here.

If a vulnerability is discovered within the first-party code (ie: application code written by the original developer of the site) then it is expected that this will be mitigated or fixed within 48hrs of discovery and reporting. 

The costs of this mitigation may be covered by the original developer's warranty, or be considered an out of scope item, depending on the terms of the governing contract.

## Automatic Updates

Due to the read-only nature of the filesystem on Platform.sh, any "auto update" functionality built into the CMS will not function. All updates must be performed locally, with the resulting changes to `composer.lock` committed to the repository.

## Maintenance Process and Procedures

Both approved CMS systems track all versions and dependencies via Composer - allowing for easy auditing of the versions of each dependency utilised by the application.

All updates should be performed locally, with the resulting changes committed to the git repo. 

All changes must then be deployed to UAT and tested on the UAT environment before progressing to the live environment.