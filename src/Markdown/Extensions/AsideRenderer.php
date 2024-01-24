<?php

namespace Ibis\Markdown\Extensions;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class AsideRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        Aside::assertInstanceOf($node);

        $attrs = $node->data->getData('attributes');
        //dd($node);
        $contents = $childRenderer->renderNodes($node->children());
        $blockQuoteContent = new HtmlElement('div', $attrs->export(), $contents);
        $blockQuoteTitle = "";
        if ($node->getTitle() !== "") {
            $blockQuoteTitle = new HtmlElement('div', ["class" => "title"], $node->getTitle());
        }

        return new HtmlElement(
            'blockquote',
            ['class' => $node->getType()],
            $blockQuoteTitle . $blockQuoteContent
        );
    }
}
