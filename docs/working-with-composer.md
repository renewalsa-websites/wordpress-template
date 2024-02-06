---
title: Working with Composer
layout: default
nav_order: 3
---

# Working with composer

Using Composer to manage dependencies is one of the core tenets of modern PHP development, and this should apply to WordPress sites as well.

WordPress is itself a 3rd-party dependency of your project, as are WordPress plugins, as such the source code should not be directly committed to version control. This is effectively a given in every other software language, along with Modern PHP projects and systems such as Craft CMS, Laravel, Symfony etc

One of the key takeaways to be aware of is that **plugin installation via the WordPress admin interface is banned**, in any environment. All plugins MUST be managed via composer and committed via version control, or they will not be able to be deployed.

## Helpful resources
It is required that any provider be comfortable and familiar with the process of using Composer with WordPress, should they apply to develop a WordPress site. 

Regardless, here are some links for potential future suppliers to upskill, so that they can meet the standards required by Renewal SA.

- [Composer Documentation](https://getcomposer.org/doc/)
 - [Why you should manage WordPress with Composer - Platform.sh](https://docs.platform.sh/guides/wordpress/composer.html)
 - [WordPress Dependencies with Composer - Bedrock Documentation - Roots Project](https://roots.io/bedrock/docs/composer/)
 - [Upgrade your WordPress site to use Composer - Platform.sh](https://docs.platform.sh/guides/wordpress/composer/migrate.html)
 - [WordPress Packagist Repository - wordpress.org plugin and theme repo mirror ](https://wpackagist.org/)

## Authentication details
When you clone the WordPress Template repository, there will be an `auth.json` file that requires details to function.

Initial uses of the `composer` CLI command may fail until the correct access key is entered into `auth.json`.

Please contact Renewal SA IT if you have not been given an access key to composer.renewalsa.sa.gov.au as part of the project on-boarding.

Sample `auth.json` file.

```
{
    "http-basic": {
        "composer.renewalsa.sa.gov.au": {
            "username": "CONTACT RSA IT FOR YOUR KEY",
            "password": "satispress"
        }
    }
}
```

Replace the `username` param above with your access key to authorise. It is fine to commit `auth.json` to your repository if the repo visibility on GitHub is set to private.

## Managing plugins

### Installing plugins from the WordPress repository
If you with to install any of the free plugins distributed on the [WordPress Plugin Repo](https://wordpress.org/plugins) then you can do so using the WordPress Packagist mirror. 

Find the plugin's "slug" (hint: it's in the URL) or search on WordPress Packagist.

```shell
# Install Yoast SEO (https://wordpress.org/plugins/wordpress-seo/)
composer require wpackagist-plugin/wordpress-seo
```
### Installing premium plugins
Renewal SA maintains licenses for a stable of popular and vetted WordPress premium plugins. It is suggested to use these vs any alternative, as each plugin is considered the preferred solution for it's task.

Eg: use Gravity Forms for forms, not Ninja Forms or Contact Form 7, use ACF Pro for custom fields or blocks, not Carbon Fields / Pods et al.

**Listing available plugins**
To find all the available plugins there are two options
```shell
composer search composer search renewalsa-plugins/
```

or visit https://composer.renewalsa.sa.gov.au/satispress/packages.json and enter the username and password in `auth.json` when prompted for the login details.

**Installing premium plugins**
The install process is the same as any other package, using the `renewalsa-plugins` vendor namespace.
```shell
# Add the Gravity Forms premium plugin
composer require renewalsa-plugins/gravityforms
```

**Procuring new plugins**
If you require a premium plugin not listed on the site, then please contact Renewal SA IT to arrange its procurement and installation - the plugin will then be available for you to install via Composer.

### Updating plugins
Composer provides an easy way to update all plugins and track the version changes via the `composer.lock` file. 

Utilising the `~x.x` or `^x.x` version constraints (next significant version) is required. Though this is the default for composer as standard when adding new packages.

Occasionally as new major versions are released, you will need to update the version constraints in `composer.json`. This is required vs using the `*` version constraint, as this can have unintended consequences. 

To update plugins within the current version constraints
```shell
composer update
```
To check which packages are outdated (useful for determining whether version constraints in `composer.json` need adjusting)
```shell
composer outdated
```
## Managing themes
In general, it is reccomended that themes be developed from scratch. Commercial themes will require that an update and maintenance plan is approved by IT, and as a rule of thumb, themes fom Themeforrest and other marketplaces are banned.

Despite this, should you require a WordPress theme, composer can manage them too.
```shell
# Installs the Twenty Twenty Four theme from wordpress.org
composer require wpackagist-theme/twentytwentyfour
```

## Managing packages
By utilising Composer, we gain access to the wider community of PHP packages, allowing them to easily be integrated into your WordPress project.

```shell
# Install the League CSV package to help read and write CSV files
composer require league/csv:^9.0
```
Some packages are included as standard in the project, see `composer.json` for more info.

### Autoloading
Composer will automatically generate autoloaders for the packages it installs, and allows us to register our own autoloaders to be included in this process.

The Composer generated `vendor/autoload.php` is loaded as part of our `wp-config.php` file, so no extra work is required.

It is highly recommended that if you are developing a custom theme you register a PSR-4 or [other supported format of autoloader](https://getcomposer.org/doc/04-schema.md#autoload) for your theme's classes.