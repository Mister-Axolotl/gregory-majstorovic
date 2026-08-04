<?php

declare(strict_types=1);

namespace App\Tests;

use App\TitreUtils;
use PHPUnit\Framework\TestCase;

final class TitreUtilsTest extends TestCase
{
    public function testNormaliseLesEspaces(): void
    {
        self::assertSame('faire le TP', TitreUtils::normaliser('  faire   le TP  '));
    }

    public function testRefuseUnTitreVide(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TitreUtils::normaliser('   ');
    }
}
