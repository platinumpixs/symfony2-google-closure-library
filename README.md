symfony2-google-closure-library
===============================

Provides a Symfony 2 Bundle and Assetic Filter for Google Closure Library

[![Build Status](https://travis-ci.org/platinumpixs/symfony2-google-closure-library.png?branch=master)](https://travis-ci.org/platinumpixs/symfony2-google-closure-library)

These options are based on the options for closure builder: https://developers.google.com/closure/library/docs/closurebuilder

```yaml
platinum_pixs_google_closure_library:
  closureCompiler:
    outputMode: compiled
    debug: false
    compilerFlags:
      - "--compilation_level=ADVANCED_OPTIMIZATIONS"
      - "--define='somevariableinside=%somevalue%'"
    externs:
      - "src/PlatinumPixs/TestBundle/Resources/javascript/loggly-externs.js"
    root:
      - "src/PlatinumPixs/SupportBundle/Resources/javascript"
```