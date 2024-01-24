<?php

namespace Ibis\Markdown\Extensions;

use League\CommonMark\Node\Block\AbstractBlock;

class Aside extends AbstractBlock
{
    final public const TYPE_NOTE = "note";

    final public const TYPE_CAUTION = "caution";

    final public const TYPE_TIP = "tip";

    final public const TYPE_DANGER = "danger";

    private readonly string $title;

    public function __construct(private $type = self::TYPE_NOTE, $title = "")
    {
        $this->title = $title === "" ? ucwords((string) $this->type) : $title;

        parent::__construct();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

}
