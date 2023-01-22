<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Domain\Syntax;

class Invert implements SyntaxInterface
{
    private SyntaxInterface $query;

    public static function query(SyntaxInterface $syntax): self
    {
        $query = new self();
        $query->query = $syntax;
        return $query;
    }

    public function build(): array
    {
        return [
            'bool' => [
                'must_not' => $this->query->build(),
            ],
        ];
    }
}
