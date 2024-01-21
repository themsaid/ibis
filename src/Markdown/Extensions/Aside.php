<?php

namespace Ibis\Markdown\Extensions;

use League\CommonMark\Node\Block\AbstractBlock;

class Aside extends AbstractBlock
{
    public function __construct()
    {
        parent::__construct();
    }

}
