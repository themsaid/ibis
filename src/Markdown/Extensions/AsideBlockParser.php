<?php

namespace Ibis\Markdown\Extensions;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

class AsideBlockParser implements BlockContinueParserInterface
{
    private readonly Aside $aside;

    public function __construct()
    {
        $this->aside = new Aside();
    }

    public function getBlock(): AbstractBlock
    {
        return $this->aside;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canHaveLazyContinuationLines(): bool
    {
        return false;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return true;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        if ($cursor->getLine() === ":::") {
            return BlockContinue::finished();
        }

        return BlockContinue::at($cursor);
    }

    public function addLine(string $line): void {}

    public function closeBlock(): void {}

}
