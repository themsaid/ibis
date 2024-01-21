<?php

namespace Ibis\Markdown\Extensions;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

class AsideParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if (!str_starts_with($cursor->getRemainder(), ':::note')) {
            return BlockStart::none();
        }

        $cursor->advanceToNextNonSpaceOrTab();
        $cursor->advanceToEnd();

        return BlockStart::of(new AsideBlockParser())->at($cursor);
    }
}
