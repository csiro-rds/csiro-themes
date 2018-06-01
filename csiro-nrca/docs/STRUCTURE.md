# Theme File Structure

This page describes the file structure of the theme.

[Back to documentation index](../README.md)

## General structure

- `conf/`
  - `app.conf` (config overrides required for the theme)
- `docs/`
  - `*.md` (these documentation pages)
- `graphics/`
  - `**/*.png` and `**/*.jpg` (image files)
- `helpers/`
  - `themeHelpers.php` (contains functions used within the theme, similar to helpers provided by CA)
- `less/`
  - `base.less` (core LESS file, includes the bootstrap library and custom overrides and components; see below)
  - `*.less` (high-level LESS files; see below)
- `views/`
  - (follows the structure of the `views` directory from the CA default theme; see below for details)
- `.gitignore` (lists build artifact files, which are not committed to version control)
- `gulpfile.js` (build automation)
- `package.json` and `package-lock.json` (automation dependencies)
- `themeInfo.conf` (CA theme information)

## Generated files

The above file structure describes the files committed the code repository.  There are additional files that are 
created by the build process that are not in version control.  See `.gitignore` for a list of ignored files.  See the 
[automation](AUTOMATION.md) page for related documentation.

## Views structure

As noted above, the `views/` directory has mostly the same file structure as the default theme, which is built into
CollectiveAccess.  Specifically, the files that are present in the CA default theme are all present in the NRCA theme.
There are some additional files in the NRCA theme:

+ Some views in the default theme were found to be very similar to each other.  These views is this theme are written
  as a call to `render()` that brings in a view from a related `common` directory.  This improves code reuse.
+ Some view components have related LESS files, which are in a nearby `less` directory.

## LESS files

There are three places where LESS files are located:

1. The primary LESS files, including the entry point `base.less` and the main overrides files `variables.less` and 
   `overrides.less`, are stored in the `less/` directory.
2. The Bootstrap LESS files, which are installed by running `npm install` as described above, and which are found in
   `node_modules/bootstrap/less/`, are `@import`ed by `base.less` and `variables.less`.
3. Page-specific styles are in `views/`, in a `less/` subdirectory sitting alongside the relevant view (`*.php`) files.
  This also includes dashboard widget views, which are in `views/widgets/<widget-name>/`.

Note that all LESS files are listed explicitly in `less/base.less`, so if adding a LESS file, it needs to be added to 
the list in that file.
