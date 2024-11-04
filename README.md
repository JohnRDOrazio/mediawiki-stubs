# Mediawiki-stubs

Mediawiki stubs with function and class signatures from Mediawiki Core, useful for completion, code inspection, type inference, doc popups, etc.

## Generate stubs

### Using composer

1. Install the package

   ```console
   mkdir mediawikistubs
   cd mediawikistubs
   composer require johnrdorazio/mediawikistubs
   ```

2. Ensure you have a local copy of `mediawiki/core`

   ```console
   mkdir mediawiki-core
   cd mediawiki-core
   git clone --depth 1 https://gerrit.wikimedia.org/r/mediawiki/core.git .
   ```

3. Create a script to generate stubs

   ```php
   <?php
   // generate_stubs.php

   require 'vendor/autoload.php';

   use Johnrdorazio\MediaWikiStubs\StubGenerator;

   $generator = new StubGenerator();
   $generator->generateStubs('./mediawiki-core');
   $generator->saveStubs('./stubs');
   ```

4. Run the script

   ```console
   php generate_stubs.php
   ```

You should now have an updated `mediawiki-stubs.php` file in the `stubs` subfolder.

### Cloning this repo

1. Clone the repository

   ```console
   mkdir mediawikistubs
   cd mediawikistubs
   git clone https://github.com/JohnRDOrazio/mediawiki-stubs.git
   ```

2. Install the composer package

   ```console
   composer install
   ```

3. Ensure you have a local copy of `mediawiki/core`

   ```console
   mkdir mediawiki-core
   cd mediawiki-core
   git clone --depth 1 https://gerrit.wikimedia.org/r/mediawiki/core.git .
   ```

4. Run the script

   ```console
   php generate_stubs.php
   ```

You should now have an updated `mediawiki-stubs.php` file in the `stubs` subfolder.
