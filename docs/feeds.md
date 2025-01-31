# Feeds File

A YAML file named `feeds.yaml` is used to configure the RSS, ATOM and JSON feeds that Vigilant should monitor. An [example file](https://github.com/VerifiedJoseph/vigilant/blob/main/feeds.example.yaml) is available.

By default Vigilant looks for a `feeds.yaml` file in the project's main folder, this behavior can be overridden with an [environment variable](environment-variables.md#feeds-file).

Each entry in the YAML file must have a name, URL and interval parameter.

```YAML
feeds:
    - name: Met Office weather warnings
      url: http://www.metoffice.gov.uk/public/data/PWSCache/WarningsRSS/Region/UK
      interval: 1900
    
    - name: GitHub Status
      url: https://www.githubstatus.com/history.rss
      interval: 600
      truncate: true
      truncate_length: 250
      active_hours:
        start_time: '09:30'
        end_time: '17:00'
```

## Parameters

### Standard

| Name              | Required | Type    | Description                                                                                     |
| ----------------- | -------- | ------- | ----------------------------------------------------------------------------------------------- |
| `name`            | **Yes**  | string  | Feed name                                                                                       |
| `url`             | **Yes**  | string  | Feed URL                                                                                        |
| `interval`        | **Yes**  | integer | Interval between feed checks in seconds. Minimum value is `300` (5 minutes).                    |
| `title_prefix`    | No       | string  | Text to prepend to notification titles.                                                         |
| `truncate`        | No       | boolean | Truncate notification text. Disabled by default. Use `truncate_length` to set custom length.    |
| `truncate_length` | No       | integer | Number of characters to truncate notification text to. Minimum is `0` and the default is `200`. |

### Notification services

| Name              | Required | Type    | Description                                                |
| ----------------- | -------- | ------- | ----------------------------------------------------------------------------- |
| `gotify_token`    | No       | string  | Gotify application token. Overrides token set with an environment variable.   |
| `gotify_priority` | No       | integer | Gotify message priority. Overrides default value and/or environment variable. |
| `ntfy_topic`      | No       | string  | Ntfy topic. Overrides topic set with an environment variable.                 |
| `ntfy_token`      | No       | string  | Ntfy access token. Overrides token set with an environment variable.          |
| `ntfy_priority`   | No       | integer | Ntfy message priority. Overrides default value and/or environment variable.   |

### Active hours

Use active hours to restrict feed monitoring to a specific time window.

Both parameters are required when configuring active hours.

| Name                     | Type   | Description                                                  |
| ------------------------ | ------ | ------------------------------------------------------------ |
| `active_hours.start_time`| string | Start time for active hours in 24-hour format. e.g: `09:00`  |
| `active_hours.end_time`  | string | End time for active hours in 24-hour format. e.g: `16:30`    |

#### Limitations

- The active hours window must start and end on the same day.
- Only 24-hour time formats are supported.
