# Frame Boilerplate

## Setup DB and content directories

Create new database and import SQL file from data/db.
mainframe-lean.sql is a barebones version with only a home page and plugins activated and configured.
mainframe-populated.sql is populated with some content and a few posts, for when a site is needed for a quick spinup.

Move uploads directory from data to site/content

## First Run

Update composer and node dependencies

`composer update && yarn upgrade` or `composer update && npm update`

Or just install the current versions

`composer install && yarn` or `composer install && npm install`

## Start

Setup webroot in Homestead or Valet to `site` folder

Start and watch front-end assets

`yarn start` or `npm start`

# Using Typescript

If using TypeScript, rename app.js to app.ts. The TypeScript compiler (tsc) will then handle the file and then pipe through the standard js pipeline (Babel, Uglify etc).

To take advantage of TypeScript autocomplete + checking with various libraries, install the corresponding TypeScript type definition file by running one of the following:

`npm install -D @types/jquery`
`npm install -D @types/lodash`
`npm install -D @types/underscore`

Many type definitions are available. See them all at https://github.com/DefinitelyTyped/DefinitelyTyped .

## Pre-launch template checklist

- [ ] 404 page
- [ ] Fav icons
- [ ] Privacy/Terms page
- [ ] Analytics Setup
- [ ] h1 on every page
- [ ] Site is tested across browsers >=IE9
