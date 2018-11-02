# php-cs-fixer-config
PHP CS Fixer config for prooph components

[![Build Status](https://travis-ci.org/prooph/php-cs-fixer-config.svg?branch=master)](https://travis-ci.org/prooph/php-cs-fixer-config)
[![Coverage Status](https://coveralls.io/repos/prooph/php-cs-fixer-config/badge.svg?branch=master&service=github)](https://coveralls.io/github/prooph/php-cs-fixer-config?branch=master)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/prooph/improoph)

It's based on the ideas of [`refinery29/php-cs-fixer-config`](https://github.com/refinery29/php-cs-fixer-config/).

## Installation

Run

```
$ composer require --dev prooph/php-cs-fixer-config
```

Add to composer.json;

```json
"scripts": {
  "check": [
    "@cs",
  ],
  "cs": "php-cs-fixer fix -v --diff --dry-run",
  "cs-fix": "php-cs-fixer fix -v --diff",
}
```
  
## Usage

### Configuration

Create a configuration file `.php_cs` in the root of your project:

```php
<?php

$config = new Prooph\CS\Config\Prooph();
$config->getFinder()->in(__DIR__);
$config->getFinder()->append(['.php_cs']);

$cacheDir = getenv('TRAVIS') ? getenv('HOME') . '/.php-cs-fixer' : __DIR__;

$config->setCacheFile($cacheDir . '/.php_cs.cache');

return $config;
```

#### Header

When you create a `.docheader` in the root of your project it will be used as header comment.

It is recommended to use the following template but you may use anything you want.

```
This file is part of `%package%`.
(c) 2016-%year% prooph software GmbH <contact@prooph.de>
(c) 2016-%year% Sascha-Oliver Prolic <saschaprolic@googlemail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
```

### Git

Add `.php_cs.cache` (this is the cache file created by `php-cs-fixer`) to `.gitignore`:

```
vendor/
.php_cs.cache
```

### Travis

Update your `.travis.yml` to cache the `php_cs.cache` file:

```yml
cache:
  directories:
    - $HOME/.php-cs-fixer
```

Then run `php-cs-fixer` in the `script` section:

```yml
script:
  - vendor/bin/php-cs-fixer fix --config=.php_cs --verbose --diff --dry-run
```

## Fixing issues

### Manually

If you need to fix issues locally, just run

```
$ composer cs-fix
```

### Pre-commit hook

You can add a `pre-commit` hook

```
$ touch .git/pre-commit && chmod +x .git/pre-commit
```
 
Paste this into `.git/pre-commit`:

```bash
#!/usr/bin/env bash

echo "pre commit hook start"

CURRENT_DIRECTORY=`pwd`
GIT_HOOKS_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

PROJECT_DIRECTORY="$GIT_HOOKS_DIR/../.."

cd $PROJECT_DIRECTORY;
PHP_CS_FIXER="vendor/bin/php-cs-fixer"

HAS_PHP_CS_FIXER=false

if [ -x "$PHP_CS_FIXER" ]; then
    HAS_PHP_CS_FIXER=true
fi

if $HAS_PHP_CS_FIXER; then
    git status --porcelain | grep -e '^[AM]\(.*\).php$' | cut -c 3- | while read line; do
        ${PHP_CS_FIXER} fix --config-file=.php_cs --verbose ${line};
        git add "$line";
    done
else
    echo ""
    echo "Please install php-cs-fixer, e.g.:"
    echo ""
    echo "  composer require friendsofphp/php-cs-fixer:2.0.0"
    echo ""
fi

cd $CURRENT_DIRECTORY;
echo "pre commit hook finish"
```
 
## License

This package is licensed using the MIT License.
