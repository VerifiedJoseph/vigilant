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
      active_hours:
        start_time: '09:30'
        end_time: '17:00'
```

## Parameters

### Standard

| Name              | Required | Description                                                                   |
| ----------------- | -------- | ----------------------------------------------------------------------------- |
| `name`            | **Yes**  | Feed name                                                                     |
| `url`             | **Yes**  | Feed URL                                                                      |
| `interval`        | **Yes**  | Interval between feed checks in seconds. Minimum value is `300` (5 minutes).  |
| `title_prefix`    | No       | Text to prepend to notification titles.                                       |

### Notification services

| Name              | Required | Description                                                                   |
| ----------------- | -------- | ----------------------------------------------------------------------------- |
| `gotify_token`    | No       | Gotify application token. Overrides token set with an environment variable.   |
| `gotify_priority` | No       | Gotify message priority. Overrides default value and/or environment variable. |
| `ntfy_topic`      | No       | Ntfy topic. Overrides topic set with an environment variable.                 |
| `ntfy_token`      | No       | Ntfy access token. Overrides token set with an environment variable.          |
| `ntfy_priority`   | No       | Ntfy message priority. Overrides default value and/or environment variable.   |

### Active hours

Use active hours to restrict feed monitoring to a specific time window.

Both parameters are required when configuring active hours.

| Name                     | Description                                                  |
| ------------------------ | ------------------------------------------------------------ |
| `active_hours.start_time`| Start time for active hours in 24-hour format. e.g: `09:00`  |
| `active_hours.end_time`  | End time for active hours in 24-hour format. e.g: `16:30`    |

#### Limitations

- The active hours window must start and end on the same day.
- Only 24-hour time formats are supported.
