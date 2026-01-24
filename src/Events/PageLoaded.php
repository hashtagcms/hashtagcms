<?php

namespace HashtagCms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PageLoaded
{
    use Dispatchable, SerializesModels;

    public $data;

    /**
     * Create a new event instance.
     *
     * @param mixed $data
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
}
