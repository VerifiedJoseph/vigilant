# Vigilant

Vigilant is an application for watching RSS/ATOM/JSON feeds and sends push notifications on new feed item.

## Documentation

- [Configuration](docs/configuration.md)
- [Feeds](docs/feeds.md)

## Requirements

- PHP >= 8.1
- Composer
- PHP Extensions:
  - [`JSON`](https://www.php.net/manual/en/book.json.php)
  - [`cURL`](https://secure.php.net/manual/en/book.curl.php)
  - [`PCRE`](https://www.php.net/manual/en/book.pcre.php)
  - [`XML`](https://www.php.net/manual/en/book.xml.php)
  - [`XMLReader`](https://www.php.net/manual/en/book.xmlreader.php)

## Dependencies

- [`verifiedjoseph/gotify-api-php`](https://github.com/VerifiedJoseph/gotify-api-php)
- [`verifiedjoseph/ntfy-php-library`](https://github.com/VerifiedJoseph/ntfy-php-library)
- [`guzzlehttp/guzzle`](https://github.com/guzzle/guzzle/)
- [`debril/feed-io`](https://github.com/alexdebril/feed-io)
- [`symfony/yaml`](https://github.com/symfony/yaml)

## License

MIT License. Please see [LICENSE](LICENSE) for more information.
