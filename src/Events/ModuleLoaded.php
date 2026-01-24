<?php

namespace HashtagCms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleLoaded
{
    use Dispatchable, SerializesModels;

    public $module;
    public $data;

    /**
     * Create a new event instance.
     *
     * @param mixed $module
     * @param mixed $data
     * @return void
     */
    public function __construct($module, $data)
    {
        $this->module = $module;
        $this->data = $data;
    }
}
