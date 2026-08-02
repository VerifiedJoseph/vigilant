<?php

declare(strict_types=1);

namespace Vigilant;

class Message
{
    private ?string $prefix = null;
    private string $title;
    private string $body;
    private string $url;
    private bool $truncate = false;
    private int $truncateLength = 200;
    private int $fallbackTruncateLength = 4000;

    /**
     * @param string $title Message title
     * @param string $body Message body
     * @param string $url Message URL
     * @param ?string $prefix Message title prefix
     * @param bool $truncate Truncate message status
     * @param ?int $truncateLength Number of characters to truncate message to.
     */
    public function __construct(
        string $title,
        string $body,
        string $url = '',
        ?string $prefix = null,
        bool $truncate = false,
        ?int $truncateLength = null
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->url = $url;
        $this->prefix = $prefix;
        $this->truncate = $truncate;

        if (is_null($truncateLength) === false && 0 < $truncateLength) {
            $this->truncateLength = $truncateLength;
        }
    }

    /**
     * Returns message title
     * @return string
     */
    public function getTitle(): string
    {
        if ($this->prefix !== null) {
            return sprintf(
                '%s %s',
                trim($this->prefix),
                trim($this->title)
            );
        }

        return $this->title;
    }

    /**
     * Returns message body
     * @return string
     */
    public function getBody(): string
    {
        return $this->truncate($this->body);
    }

    /**
     * Returns message URL
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Truncate message body
     * @param string $text
     * @return string
     */
    private function truncate(string $text): string
    {
        $length = $this->truncateLength;

        if (strlen($text) > $this->fallbackTruncateLength) {
            $this->truncate = true;
            $length = $this->fallbackTruncateLength;
        }

        if (strlen($text) <= $length || $this->truncate === false) {
            return $text;
        }

        $text = substr($text, 0, $length);
        $breakpoint = strrpos($text, '.');

        if ($breakpoint !== false) {
            $text = substr($text, 0, $breakpoint);
        }

        return $text . '...';
    }
}
