<?php

namespace Tests\Unit;

use App\Enums\PermissionLevel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PermissionLevelTest extends TestCase
{
    public static function matrix(): array
    {
        // level => [canWriteContent, canWriteSite, canWriteManagement]
        return [
            'Viewer (0) writes nothing' => [PermissionLevel::Viewer, false, false, false],
            'Editor (1) writes only content' => [PermissionLevel::Editor, true, false, false],
            'Administrator (3) writes content and site, not management' => [PermissionLevel::Administrator, true, true, false],
            'Supervisor (7) writes everything' => [PermissionLevel::Supervisor, true, true, true],
        ];
    }

    #[DataProvider('matrix')]
    public function test_permission_matrix(
        PermissionLevel $level,
        bool $expectedContent,
        bool $expectedSite,
        bool $expectedManagement
    ): void {
        $this->assertSame($expectedContent, $level->canWriteContent());
        $this->assertSame($expectedSite, $level->canWriteSite());
        $this->assertSame($expectedManagement, $level->canWriteManagement());
    }
}
