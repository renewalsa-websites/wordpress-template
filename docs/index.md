---
title: Home
layout: home
nav_order: 1
---

# Renewal SA WordPress Template Docs

The WordPress boilerplate acts as a blank canvas to allow agencies to easily spin up new WordPress projects that conform with requirements for Renewal SA and the Platform.sh hosting environment that sites are deployed to.

This repository is intended to be cloned, then detached from this git remote.  You are free to change from there as you see fit - there is no need to keep this repo as an upstream dependency, the project is not designed to "ship updates". 

## Getting started

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
Finally to finish the setup, we need to add out database credentials to the current site. For local development we use a `.env` file. Any enviornment on Platform.sh will have these environment variables injected into the runtime, and these can be managed from Platform.sh via the CLI or control panel.

For local dev
```bash
# Copy the example .env.example file
cp .env.example .env

# Edit the .env using VSCode, Vim, Nano, whatever works for you...
```

### Development Environment
This project will work fine with many different development environments. We recommend Laravel Valet, or DDev.

The specifics of getting this project running in the environment are not covered in this guide - it is assumed the devs will have sufficient knowledge of their environment to make it work.

One important point is that the `site` folder should be the webroot.

Laravel Valet users may be interested in this Enhanced WordPress Driver which is designed to deal with composer / bedrock style WordPress sites.

## A starting point
This boilerplate is intentionally sparse in order to not force a particular toolchain onto developers. It is mainly concerned with the structure of the project, and the use of composer for managing WordPress, Plugins and 3rd party libraries.

### Key principles
This project is based around the following principles
- Track all code for the project in version control
- Keep 3rd party dependency code (WordPress core, Plugins) out of the repo
- Utilise Composer with a Bedrock like setup with WordPress in a subdirectory
- Include some mandatory plugins for security and compliance

### Building your site
This project does not provide a theme or dictate your front-end toolchain. 

It is recommended/highly preferred to create a WordPress theme from scratch for each project vs using commercial themes.

In most modern projects there will be some kind of processing of javascript, css files etc - you are free to implement what you choose. Tailwind.css is a good recommendation for styling though you will often need to use the `@apply` rules to target markup provided by plugins.

Please see [Best Practices](#) for more information on recommended development practices.

## Next Steps

### Develop your site
The site should be developed locally by the developers, with changes committed to git and pushed to GitHub

Please see [Best Practices](#) for more information on recommended development practices.

### Deploy to Platform.sh
When it is time to show the client, it's reccomended to deploy to the Platform.sh server for previewing etc.

The default deployment on platform.sh runs off the `live` branch of the connected repository. Push your changes to a branch called `live` to trigger the deployment.

After the site is live, you should create and use the `uat` branch first for testing and to get sign-off on any changes, and then push to `live` when you are satisfied.

Please see [Deploying to Platform.sh] for more detail.

----

[^1]: [It can take up to 10 minutes for changes to your site to publish after you push the changes to GitHub](https://docs.github.com/en/pages/setting-up-a-github-pages-site-with-jekyll/creating-a-github-pages-site-with-jekyll#creating-your-site).

[Just the Docs]: https://just-the-docs.github.io/just-the-docs/
[GitHub Pages]: https://docs.github.com/en/pages
[README]: https://github.com/just-the-docs/just-the-docs-template/blob/main/README.md
[Jekyll]: https://jekyllrb.com
[GitHub Pages / Actions workflow]: https://github.blog/changelog/2022-07-27-github-pages-custom-github-actions-workflows-beta/
[use this template]: https://github.com/just-the-docs/just-the-docs-template/generate
