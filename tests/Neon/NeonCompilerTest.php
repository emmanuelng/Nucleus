<?php

declare(strict_types=1);

namespace Tests\Neon;

require_once __DIR__ . '/callbacks/callbacks.php';

use Nucleus\Neon\NeonCompiler;
use PHPUnit\Framework\TestCase;

/**
 * Tests the NEON compiler.
 */
class NeonCompilerTest extends TestCase
{
    /**
     * The path to the routes directory.
     */
    const NEON_PATH = __DIR__ . '\routes';

    /**
     * Tests whether the compiler is able to compile valid files.
     *
     * @return void
     */
    public function testCompilesValidRoutes(): void
    {
        $path = self::NEON_PATH . '\valid';

        $files  = glob($path . '\*.route.neon');
        $routes = NeonCompiler::compile($path);

        $this->assertSameSize($files, $routes);
    }
}
