# Vigilant

Vigilant is a PHP script for monitoring RSS/ATOM/JSON feeds and sending push notifications on new entries.

## Documentation

- [Installation](docs/install.md)
- [Configuration](docs/configuration.md)
- [Feeds File](docs/feeds.md)

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
