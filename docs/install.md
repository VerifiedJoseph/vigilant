# Installation

The easiest way to deploy Vigilant is with Docker, however it can be installed manually.

## Docker

[Add info]

## Manually

Clone the repository.

`git clone https://github.com/VerifiedJoseph/vigilant.git`

Install dependencies with composer.

`composer install --no-dev`

There are two scripts that can be used to run Vigilant: `vigilant.php` and `daemon.php`.

`vigilant.php` is designed to be used with task scheduler like cron, whilst `daemon.php` is designed to used as a daemon process.

Cron example:
```
5 * * * * php vigilant.php
```
When Vigilant running via a task scheduler, the script should ran at minimum of every 5 minutes.
