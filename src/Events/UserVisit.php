<?php

namespace HashtagCms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserVisit
{
    use Dispatchable, SerializesModels;

    public $type;
    public $id;

    /**
     * Create a new event instance.
     *
     * @param string $type
     * @param int $id
     * @return void
     */
    public function __construct(string $type, int $id)
    {
        $this->type = $type;
        $this->id = $id;
    }
}
