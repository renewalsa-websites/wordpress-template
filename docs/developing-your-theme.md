---
title: Developing your theme
layout: default
nav_order: 4
---

# Developing your theme

Once you have set up your project and installed some dependencies, it's time to get to work on the unique portions of a project. Ultimately how to develop your theme is up to each agency, but this document offers some guidance to ensure a high quality output.

## General guidelines
When developing a theme for a project please follow the following guidelines
 - Avoid commercial themes from Themeforrest / Marketplaces
 - All websites must comply with the relevant government specifications including accessibility
 - It is recommended to avoid page builder plugins - Elementor, Beaver Builder, Divi theme et al
 - The finished website should score over 85 in Google Lighthouse for both mobile and desktop

## Using 3rd Party Themes
Overall it is not recommended to utilise 3rd party themes. 

Should a 3rd Party theme be approved by IT and utilised, then you MUST create and use a child-theme to manage any customisations or changes.

{:.warning}
Developers must NEVER alter the core code of a 3rd party theme as this can prevent updates in the future - always use a child theme.
 
## Managing Custom Data / Layouts
For managing data entry and page layouts, it is recommended to use Advanced Custom Fields Pro. Utilising the Block Editor (including custom blocks) is also suitable.

### Using ACF Pro
When using ACF Pro within WordPress please follow the following guidelines.
1. ACF config must be tracked in version control - this can be via their ACF JSON feature, or by defining the fields within your code.
2. If utilising ACF pro, it is recommended that it be installed as a must-use plugin - the default `composer.json` config in this project will install it as such.

If you are using ACF heavily, particularly the [Flexible Content](https://www.advancedcustomfields.com/resources/flexible-content/) field, it's recommended to use [ACF builder](https://github.com/StoutLogic/acf-builder) to define the fields in code, as it offers levels of reuse and composition way above what be achieved with ACF's native "clone" fields.

### Recommended tools and frameworks
There are two official Content Management Systems approved for use within Renewal SA -  WordPress and Craft CMS. With this in mind, it's beneficial for both systems to utilise a similar approach to developing the front-end.

Craft CMS uses Twig for all templating, and it is recommended that you use Twig for templating in WordPress as well. To use Twig we recommend the [Timber Library / Framework](https://timber.github.io/docs/v2/) for WordPress. Timber works particularly well with Advanced Custom Fields Pro (and the flexible content field) to provide a "craft-like" experience in the development of the site.

Developers can roll their own front-end toolchain, here's some links to interesting tools at the time of writing (Q1 2024).

 - [Tailwind CSS](https://tailwindcss.com/)
 - [AplineJS](https://alpinejs.dev/)
 - [Vite](https://vitejs.dev/)
 - [Laravel Mix](https://laravel-mix.com/)
 - [Frame Build](https://npm.io/package/frame-build) (Older, used by Frame Creative for some projects)
 - [Agency Webpack Mix Config](https://github.com/ben-rogerson/agency-webpack-mix-config) (Older, created at Simple, Independently maintained now)