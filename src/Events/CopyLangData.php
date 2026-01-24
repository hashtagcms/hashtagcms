<?php

namespace HashtagCms\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CopyLangData
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sourceLang;
    public $targetLang;
    public $tables;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($sourceLang, $targetLang, $tables)
    {
        $this->sourceLang = $sourceLang;
        $this->targetLang = $targetLang;
        $this->tables = $tables;
    }
}
