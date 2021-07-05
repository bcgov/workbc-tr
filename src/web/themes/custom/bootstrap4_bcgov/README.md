# Bootstrap 4 BCGov - a Bootstrap 4 subtheme for Drupal 8/9

This theme adds some basic BC Government styling to Bootstrap 4.

This theme can be used on its own, but if any custom styling is required, it should be done in a subtheme of this theme.

## Including this theme in your project

1. Add this repository to your project's `composer.json` in the `repositories` array, eg:

```json
  "repositories": [
    {
      "type": "composer",
      "url": "https://packages.drupal.org/8"
    },
    {
      "type": "composer",
      "url": "https://asset-packagist.org"
    },
    {
      "type": "vcs",
      "url": "git@rassilon.canadacentral.cloudapp.azure.com:cgiweb/bootstrap4_bcgov.git"
    }
  ],
```

2. Install this theme (it will include its parent, bootstrap4, as a dependency):

```bash
    $ composer require 'cgiweb/bootstrap4_bcgov'
```

3. Add the path to this theme (eg `/web/themes/custom/bootstrap4_bcgov/`) to your project's `.gitignore`

## Subtheming Instructions

### Create a subtheme of this theme by following these steps:

* Copy `_SUBTHEME` folder to the location of custom folder
* Rename `SUBTHEME` instances to your theme, e.g.  if your theme called `b4theme`:
  * Rename `SUBTHEME.info._yml` to `b4theme.info.yml` and its content
  * Rename `SUBTHEME.libraries.yml` to `b4theme.libraries.yml`
  * Rename `SUBTHEME.breakpoints.yml` to `b4theme.breakpoints.yml`
  * Change all occurence of `SUBTHEME` by `b4theme` in `b4theme.breakpoints.yml`
  * Rename `SUBTHEME.theme` to `b4theme.theme` and its comments
  * Update name in `package.json` and `package-lock.json`
* Update import path in `SUBTHEME/scss/style.scss` to this theme path (if not matching already)
  * Should look like `@import "../../../../themes/custom/bootstrap4_bcgov/scss/style";`.

### Customisations

* Take a look at the .scss files of this theme to see some ways to override existing styles - add your own scss in your subtheme.
* Also look at the parent of this theme, bootstrap4.

### Tools

* NPM package info is included - run `npm install` in your local or dev environment to get started (requires npm)
* After installation, `npm run build:sass` will compile your scss into `css/style.css`

## Block Layout

Recommended starting block layout for this theme and subthemes:

##### Header
* Branding
##### Main navigation region
* Main navigation
##### Additional navigation region (eg search form, social icons, etc) 
* User account menu
##### Main content
* Status messages
* Page title
* Tabs
* Help
* Primary admin actions
* Main page content