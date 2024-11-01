# Installation

## Docker compose

```yaml
version: '3'
services:
  vigilant:
    image: ghcr.io/verifiedjoseph/vigilant:1.4.5
    environment:
      - VIGILANT_NOTIFICATION_SERVICE=ntfy
      - VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.sh/
      - VIGILANT_NOTIFICATION_NTFY_TOPIC=testingtesting
    volumes:
      - "./feeds.yaml:/app/feeds.yaml"
    restart: unless-stopped
    security_opt:
      - no-new-privileges:true
```

## Manually

1) Download the [latest release](https://github.com/VerifiedJoseph/vigilant/releases/latest) to your server and extract the zip archive.

2) Setup the feeds to monitor using a [feeds file](feeds.md).

3) Set the configuration using [environment variables](environment-variables.md) with `config.php` copied from [`config.example.php`](https://github.com/VerifiedJoseph/vigilant/blob/main/config.example.php).

	```
	cp config.example.php config.php
	```

4) Create a scheduled task with cron (below) or similar that runs `vigilant.php` at least every 5 minutes.

	```
	*/5 * * * * php path/to/vigilant.php
	```

