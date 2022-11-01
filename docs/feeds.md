# Feeds 

A YAML formatted file named `feeds.yaml` is used to configure the RSS/ATOM and JSON feeds Vigilant should watch.

By default Vigilant looks for a `feeds.yaml` file in the project's main folder, this behavior can be overridden with an [environment variable](configuration.md#feeds-file).

An example file is include in the main folder: [`feeds.example.yaml`](../feeds.example.yaml).

Each entry in the YAML file must have name, URL and an interval value, all other values are optional.

```YAML
name: Met Office weather warnings
url: http://www.metoffice.gov.uk/public/data/PWSCache/WarningsRSS/Region/UK
interval: 1900
```

| Name              | Required | Description                                                                |
| ----------------- | -------- | -------------------------------------------------------------------------- |
| `name`      		| Yes      | Feed name                                                                  |
| `url`       		| Yes      | Feed URL                                                                   |
| `interval`        | Yes      | Interval between feed checks in seconds. Min value is `300` (5 minutes).   |
| `gotify_token`    | No       | Gotify application token. Overrides token set with a environment variable. |
| `gotify_priority` | No       | Gotify message priority.                                                   |
| `ntfy_topic`      | No       | Ntfy topic. Overrides topic set with a environment variable.               |
| `ntfy_priority`   | No       | Ntfy message priority.                                                     |
