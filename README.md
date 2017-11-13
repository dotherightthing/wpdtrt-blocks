# DTRT WP Blocks

Demo plugin which uses [wpdtrt-plugin](https://github.com/dotherightthing/wpdtrt-plugin).

## Setup

```
// 1. Install PHP dependencies
composer install

// 2. Install Node dependencies into the parent plugin's folder
npm --prefix ./vendor/dotherightthing/wpdtrt-plugin/ install ./vendor/dotherightthing/wpdtrt-plugin/

// 3. Run the parent plugin's Gulp tasks against the contents of the child plugin's folder
// 4. Watch for changes to the child plugin's folder
gulp --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./
```

## Usage

Please read the [WordPress readme.txt](readme.txt) for usage instructions.
