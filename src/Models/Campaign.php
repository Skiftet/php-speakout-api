<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Models;

/**
 *
 */
class Campaign extends BaseModel
{
    public function url(): string
    {
        return $this->client()->endpoint().'/campaigns/'.urlencode($this['slug']);
    }
}
