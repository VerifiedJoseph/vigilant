<?php

namespace Vigilant;

class Message
{
    private string $title;
    private string $body;
    private string $url;

    /**
     * @param string $title Message title
     * @param string $body Message body
     * @param string $url Message URL
     */
    public function __construct(string $title, string $body, string $url = '')
    {
        $this->title = $title;
        $this->body = $body;
        $this->url = $url;
    }

    /**
     * Returns message title
     * @return string
     */
    public function getTitle(): string
    {
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
