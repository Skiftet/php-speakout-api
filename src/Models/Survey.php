<?php
declare(strict_types=1);

namespace Skiftet\Speakout\Models;

/**
 *
 */
class Survey extends BaseModel
{
    public function url(): string
    {
        return $this->client()->endpoint().'/surveys/'.urlencode($this['id']);
    }
}
