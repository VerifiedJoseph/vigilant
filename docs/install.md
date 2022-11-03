# Installation

The easiest way to deploy Vigilant is with Docker, it also can be installed manually.

## Docker

Configure Vigilant using [environment variables](configuration.md) and a [feeds file](feeds.md) and then run the prebuilt image.

```
docker run -it -v $(pwd)/feeds.yaml:/app/feeds.yaml --env-file .env ghcr.io/verifiedjoseph/vigilant:latest
```

Or use a docker compose file:

```
version: '3'
services:
  vigilant:
    image: ghcr.io/verifiedjoseph/vigilant:latest
    environment:
      - VIGILANT_NOTIFICATION_SERVICE=ntfy
      - VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/
      - VIGILANT_NOTIFICATION_NTFY_TOPIC=exampleTopic
    volumes:
      - "./feeds.yaml:/app/feeds.yaml"
    restart: unless-stopped
```

## Manually

Clone the repository.

`git clone https://github.com/VerifiedJoseph/vigilant.git`

Install dependencies with composer.

`composer install --no-dev`

There are two scripts that can be used to run Vigilant: `vigilant.php` and `daemon.php`.

`vigilant.php` is designed to be used with a task scheduler like cron, whilst `daemon.php` is designed to used as a daemon process.

Cron example:
```
5 * * * * php vigilant.php
```
When Vigilant running via a task scheduler, the script should ran at minimum of every 5 minutes.
