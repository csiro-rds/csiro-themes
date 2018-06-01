# Build Automation

The build process is automated using [NodeJS](https://nodejs.org/) and the [gulp](https://gulpjs.com/) task runner.

[Back to documentation index](../README.md)

## Setup

A recent NodeJS environment is required to run the build and automation tools, [nvm](https://github.com/creationix/nvm)
is recommended.  To install dependencies:

```
npm install --global gulp-cli
npm install
```

The first command installs the `gulp` command-line interface globally; the second installs a few local dependencies.

## Tasks

There are three top-level `gulp` tasks plus the default task:

* `gulp` the default task runs a build and then starts watching for changes.  This is the easiest way to run local
  build automation.
* `gulp clean` removes the build artifacts created by the `build` task.  It generally won't be necessary to run this
  command directly, it is called when necessary.
* `gulp build` runs the build process, creating the build artifacts.
* `gulp watch` starts a file watcher that runs `gulp build` automatically whenever any source file changes.

The `clean` and `build` tasks are comprised of individual sub-tasks, which can be run individually if required:

* `gulp clean:less` and `gulp build:less` remove and create (respectively) the CSS files based on the source LESS files.
* `gulp clean:js` and `gulp build:js` remove and create (respectively) the JavaScript file from the Bootstrap source
  (this simply copies the file to the right place relative to the theme directory).
* `gulp clean:fonts` and `gulp build:fonts` remove and create (respective) the font files from the Bootstrap source
  (this simply copies the file to the right place relative to the theme directory).
