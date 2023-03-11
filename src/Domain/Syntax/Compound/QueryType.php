<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Domain\Syntax\Compound;

interface QueryType
{
    public const MUST_NOT = 'must_not';

    public const MUST = 'must';

    public const SHOULD = 'should';

    public const FILTER = 'filter';

    public const ALL = [self::MUST, self::MUST_NOT, self::SHOULD, self::FILTER];
}
