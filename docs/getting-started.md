---
title: Getting started
layout: default
nav_order: 2
---
# Getting started

### Clone and init your repository
To utilise this project, clone the git repo to your local machine, remove the current git repo and initialise a new one. Renewal SA should provide you with a repository within their GitHub organisation to act as the primary `origin` remote. 
```bash
# Clone the Repo
git clone https://github.com/renewalsa-websites/wordpress-template.git my-project
# Navigate into the project folder
cd my-project
# Remove the current git repo
rm -rf .git/
# Start a new git repo
git init
# Initial Commit
git add .
git commit -m "Initial commit"

# Add git remote in Renewalsa websites organisation (to be provided to you)
# Push initial code to the remote
git remote add origin https://github.com/renewalsa-websites/my-project.git
git branch -M master
git push -u origin master

```

### Install WordPress and plugins

Edit the `auth.json` file to update the credentials for the private Renewal SA plugins repository. These credentials will be provided to you. This will be necessary to access and install any of the plugins in the `renewalsa-plugins` vendor namespace. Renewal SA utilises this for paid 3rd party preferred plugins such as Advanced Custom Fields, Gravity Forms, SearchWP and more.

Sample `auth.json`
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

Once you have the appropriate credentials, you can utilise composer to install WordPress core and the starter plugins from this boilerplate. 

```bash
# Assumed current working directory is 'my-project' or the project root

#Install WordPress and Plugins
composer install

#Update them to the latest version
composer update

#Check for outdated plugins and packages
composer outdated
```

By utilising composer, we can be sure that the exact same versions of WordPress and Plugins are utilised between local, UAT, and Live Environments. 

It also allows us to track the addition of any new plugin to the site in version control, as required by Renewal SA.

### Finishing the setup
Finally to finish the setup, we need to add our database credentials to the current site. It is up to you to create the database and associated user necessary for this, as the method for doing so will vary depending on your development environment.

For local development we use a `.env` file. Any enviornment on Platform.sh will have these environment variables injected into the runtime, and these can be managed from Platform.sh via the CLI or control panel.

For local dev
```bash
# Copy the example .env.example file
cp .env.example .env

# Edit the .env using VSCode, Vim, Nano, whatever works for you...
```

Finally you will need to run the WordPress install to populate the database etc

### Development Environment
This project will work fine with many different development environments. We recommend [Laravel Valet](https://laravel.com/docs/10.x/valet), or [DDev](https://ddev.com/).

The specifics of getting this project running in the environment are not covered in this guide - it is assumed the devs will have sufficient knowledge of their environment to make it work.

One important point is that the `site` folder should be the webroot.

Laravel Valet users may be interested in this [Enhanced WordPress Driver](https://github.com/framecreative/frame-valet-drivers) which is designed to deal with composer / bedrock style WordPress sites.