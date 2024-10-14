<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message;

use App\Message\ProjectTasksUpdate;
use App\Tests\Unit\UnitTestCase;

final class ProjectTaskUpdateTest extends UnitTestCase
{
    public function testGettersReturnSuccess(): void
    {
        $projectId = 1;

        $message = new ProjectTasksUpdate(projectId: $projectId);

        $this->assertSame($projectId, $message->getProjectId());
    }
}
