<?php


namespace App\Models\Values;


class MailFormat
{
    const TYPE_MARKDOWN = 1;
    const TYPE_HTML = 2;
    const TYPE_TEXT = 3;

    const TYPES = [
        self::TYPE_MARKDOWN,
        self::TYPE_HTML,
        self::TYPE_TEXT
    ];
}
