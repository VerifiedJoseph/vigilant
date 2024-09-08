<?php

namespace Vigilant;

class Message
{
    private ?string $prefix = null;
    private string $title;
    private string $body;
    private string $url;

    /**
     * @param string $title Message title
     * @param string $body Message body
     * @param string $url Message URL
     * @param ?string $prefix Message title prefix
     */
    public function __construct(string $title, string $body, string $url = '', ?string $prefix = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->url = $url;
        $this->prefix = $prefix;
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
        return $this->body;
    }

    /**
     * Returns message URL
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
