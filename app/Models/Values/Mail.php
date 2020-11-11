<?php
declare(strict_types=1);

namespace App\Models\Values;


class Mail
{
    const TYPE_MARKDOWN = "markdown";
    const TYPE_HTML     = "html";
    const TYPE_TEXT     = "text";

    const TYPES = [
        self::TYPE_MARKDOWN,
        self::TYPE_HTML,
        self::TYPE_TEXT
    ];

    /**
     * @var string
     */
    private $from;
    /**
     * @var string
     */
    private $to;
    /**
     * @var array
     */
    private $cc;
    /**
     * @var string
     */
    private $subject;
    /**
     * @var string
     */
    private $body;
    /**
     * @var string|null
     */
    private $format;

    /**
     * Create a new message instance.
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param array|null $cc
     * @param string|null $format
     */
    public function __construct(
        string $from,
        string $to,
        string $subject,
        string $body,
        ?array $cc,
        ?string $format
    )
    {
        $this->from    = $from;
        $this->to      = $to;
        $this->subject = $subject;
        $this->body    = $body;
        $this->cc      = $cc ?? [];
        $this->format  = $format ?? self::TYPE_TEXT;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
