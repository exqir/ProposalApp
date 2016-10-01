# ProposalApp

## Architecture
The application is divided into the backend part, using php, and the frontend or app part, using javascript/coffeescript and angular as framework.

| /example_database
| /src
| - /app
| -- /assets
| -- /partials
| -- /script
| -- /styles
| -- index.html
| - /backend
| -- /api
| -- /db
| -- /location
| -- /organization
| -- /proposal
| -- /subject
| -- /util
| -- config.php
| - proposalCollector.php
| .htaccess
| credentials.php
| gulpfile.js
| package.json
| path-config.js
| README.md

## Getting-Started
Node.JS and npm are required to use the build process and to compile the app. You can get it [here](https://nodejs.org/en/) or install it via homebrew `brew install node`.

To install the JS dependencies just use `npm install`. The php dependencies, currently only Flight as REST-API, are not included. Flight is located in `/src/backend/api/flight`.

In `htaccess` the second line `RewriteBase /ProposalApp/dist/backend/api` has to be set to the path of the backend folder of the server, this is necessary resolving the routes of the REST-API.

`credentials.php` contains the credentials of the SQL database, as well as the Google API KEY, used for the location service (`/src/backend/location). This file is contained in the gitignore and the file provided in the repository only contains localhost parameters.
*DO NOT* commit changes to this file into the repository. This file should only be push to the server once and it should only be edited on the server. It is not copied to the `/dist` folder during the normal build process.

[Gulp](http://gulpjs.com/) is used as build tool. To build the app and create the `/dist` directory use `gulp build`.
There are other build steps available:

compand | description
--------|------------
gulp dev | creates `/dist`, also copying credentials.php - to allow testing with a local database
gulp build | creates `dist and renames `proposalCollector.php` to `proposalCollector.php70` to set the php version to 7.X
gulp deploy | *WIP* first runs gulp build and pushes the result to the server defined in credentials.js

The `path-config.js` defines the paths used by the gulpfile. Dependencies that should be copied to `/dist/app/vendor` must be defined here, as well as in the `gulpfile.js`.

The `config.php` in `/src/backend` contains the urls for the proposals and subjects that should be parsed and collected by the application.

In `/example_database/usi_proposal.sql` is the database structure is defined including pre filled organization types and corresponding keywords.

