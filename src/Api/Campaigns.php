<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Api;

class Campaigns extends BaseResource
{
    public function subResourcePaths(): array
    {
        return ['actions'];
    }

}
