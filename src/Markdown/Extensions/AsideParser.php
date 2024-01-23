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
        $type = Aside::TYPE_NOTE;
        $title = "";
        $isAside = false;
        if (str_starts_with($cursor->getRemainder(), ':::note')
        || str_starts_with($cursor->getRemainder(), ':::notice')) {
            $type = Aside::TYPE_NOTE;
            $title = "Note";
            $isAside = true;
        }

        if (str_starts_with($cursor->getRemainder(), ':::warning') ||
            str_starts_with($cursor->getRemainder(), ':::caution')) {
            $type = Aside::TYPE_CAUTION;
            $title = "Caution";
            $isAside = true;
        }

        if (str_starts_with($cursor->getRemainder(), ':::tip')) {
            $type = Aside::TYPE_TIP;
            $title = "Tip";
            $isAside = true;
        }

        if (str_starts_with($cursor->getRemainder(), ':::danger')) {
            $type = Aside::TYPE_DANGER;
            $title = "Danger";
            $isAside = true;
        }

        if ($isAside) {
            $pattern = '/\[([^]]+)\]/';
            if (preg_match($pattern, $cursor->getRemainder(), $matches)) {
                $title = $matches[1];
            }

            $cursor->advanceToNextNonSpaceOrTab();
            $cursor->advanceToEnd();

            return BlockStart::of(new AsideBlockParser($type, $title))->at($cursor);
        }

        return BlockStart::none();



    }
}
