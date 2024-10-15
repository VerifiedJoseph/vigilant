
The repository includes two [dev containers](https://containers.dev/) to help streamline development.

* `php-container` is used for writing and testing Vigilant.
* `python-container` is used for writing and building the documentation.

## PHP Container

`php-container` uses [composer](https://getcomposer.org/) to manage dependencies and run code quality tools.

### Commands

Install dependencies
<small>(Dependencies are automatically installed when the container starts)</small>
```
composer install
```

Analysis code using PHP_CodeSniffer and PHPStan
```
composer lint
```

Fix code formatting using PHP_CodeSniffer
```
composer fix
```

Test code using PHPUnit
```
composer test
```

## Python Container

`python-container` uses [Pipenv](https://pipenv.pypa.io/) to manage dependencies and run MkDocs commands.

### Commands

Install dependencies
<small>(Dependencies are automatically installed when the container starts)</small>
```
pipenv install
```

Run the MkDocs development server
```
pipenv run mkdocs serve
```

Build the MkDocs documentation
```
pipenv run mkdocs build
```
