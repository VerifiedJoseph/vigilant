# Configuration

The preferred method to adjust the configuration of Vigilant is with environment variables.

Alternatively, you can use `config.php` (copied from [`config.example.php`](../config.example.php)) to set the variables.

## Feeds File

By default Vigilant looks for a `feeds.yaml` file in the project's main folder, this behavior can be overridden by setting the environment variable `VIGILANT_FEEDS_FILE`.

| Name                  | Description                  |
| --------------------- | ---------------------------- |
| `VIGILANT_FEEDS_FILE` | Path of a `feeds.yaml` file. |

## Notification Service

To send push notifications, a notification service is required. Vigilant supports [Gotify](https://gotify.net) or [ntfy.sh](https://ntfy.sh).

| Name                            | Description                                                        |
| ------------------------------- | ------------------------------------------------------------------ |
| `VIGILANT_NOTIFICATION_SERVICE` | Notification service to use. Supported values: `gotify` or `ntfy`. |

### Gotify

To use Gotify, a URL and application token must be given.

| Name                                 | Description                |
| ------------------------------------ | -------------------------- |
| `VIGILANT_NOTIFICATION_GOTIFY_URL`   | URL used to access Gotify. |
| `VIGILANT_NOTIFICATION_GOTIFY_TOKEN` | Gotify application token.  |

### Ntfy

To use Ntfy, a URL and topic must be given, all other environment variable are optional.

| Name                                 | Description            |
| ------------------------------------ | ---------------------- |
| `VIGILANT_NOTIFICATION_NTFY_URL`   | URL used to access Ntfy. |
| `VIGILANT_NOTIFICATION_NTFY_TOPIC` | Ntfy topic.              |

Vigilant can be used a with ntfy server that has password or [access token](https://docs.ntfy.sh/config/#access-tokens) authentication enabled.

| Name                         | Description                                           |
| ---------------------------- | ----------------------------------------------------- |
| `NOTIFICATION_NTFY_AUTH`     | Authentication method. Values: `password` or `token`. |
| `NOTIFICATION_NTFY_USERNAME` | Ntfy authentication username.                         |
| `NOTIFICATION_NTFY_PASSWORD` | Ntfy authentication password.                         |
| `NOTIFICATION_NTFY_TOKEN`    | Ntfy access token.                                    |
