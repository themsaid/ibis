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
        $contents = $childRenderer->renderNodes($node->children());

        return new HtmlElement(
            'blockquote',
            ['class' => 'notice'],
            new HtmlElement('div', $attrs->export(), $contents)
        );
    }
}
