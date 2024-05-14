# Feeds File

A YAML file named `feeds.yaml` is used to configure the RSS, ATOM and JSON feeds that Vigilant should monitor.

By default Vigilant looks for a `feeds.yaml` file in the project's main folder, this behavior can be overridden with an [environment variable](configuration.md#feeds-file).

An [example file](../feeds.example.yaml) is available.

Each entry in the YAML file must have a name, URL and interval parameter. All other parameters are optional.

```YAML
feeds:
    - name: Met Office weather warnings
      url: http://www.metoffice.gov.uk/public/data/PWSCache/WarningsRSS/Region/UK
      interval: 1900
    
    - name: GitHub Status
      url: https://www.githubstatus.com/history.rss
      interval: 600
```

| Name              | Required | Description                                                                   |
| ----------------- | -------- | ----------------------------------------------------------------------------- |
| `name`            | **Yes**  | Feed name                                                                     |
| `url`             | **Yes**  | Feed URL                                                                      |
| `interval`        | **Yes**  | Interval between feed checks in seconds. Minimum value is `300` (5 minutes).  |
| `title_prefix`    | No       | Text to append to the front of a message's title.                             |
| `gotify_token`    | No       | Gotify application token. Overrides token set with an environment variable.   |
| `gotify_priority` | No       | Gotify message priority. Overrides default value and/or environment variable. |
| `ntfy_topic`      | No       | Ntfy topic. Overrides topic set with an environment variable.                 |
| `ntfy_token`      | No       | Ntfy access token. Overrides token set with an environment variable.          |
| `ntfy_priority`   | No       | Ntfy message priority. Overrides default value and/or environment variable.   |
