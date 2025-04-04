# Vigilant

<div>
    <img alt="igilant running in a terminal and a phone receiving a push notification sent by Vigilant using Gotify." src="img/readme-header.png">
</div>

Vigilant is RSS/ATOM/JSON feed monitor that sends push notifications on new entries using [Gotify](https://gotify.net/) or [ntfy.sh](https://ntfy.sh).

## Documentation

- [Installation](install.md)
- [Environment variables](environment-variables.md)
- [Feeds file](feeds.md)
- [Dev Containers](dev-containers.md)

## Requirements

* PHP >= 8.2
* Composer
* PHP Extensions:
    * [`JSON`](https://www.php.net/manual/en/book.json.php)
    * [`cURL`](https://secure.php.net/manual/en/book.curl.php)
    * [`PCRE`](https://www.php.net/manual/en/book.pcre.php)
    * [`XML`](https://www.php.net/manual/en/book.xml.php)
    * [`XMLReader`](https://www.php.net/manual/en/book.xmlreader.php)

## Dependencies

- [`verifiedjoseph/gotify-api-php`](https://github.com/VerifiedJoseph/gotify-api-php)
- [`verifiedjoseph/ntfy-php-library`](https://github.com/VerifiedJoseph/ntfy-php-library)
- [`guzzlehttp/guzzle`](https://github.com/guzzle/guzzle/)
- [`debril/feed-io`](https://github.com/alexdebril/feed-io)
- [`symfony/yaml`](https://github.com/symfony/yaml)

## Credits

Logo designed by [@hazzuk](https://github.com/hazzuk) <small>(licensed under [CC BY 4.0 International](licenses.md#logo-license))</small>
