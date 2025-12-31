<?php

namespace MarghoobSuleman\HashtagCms\Listeners;

use MarghoobSuleman\HashtagCms\Events\CopyLangData;
use MarghoobSuleman\HashtagCms\Models\Lang;

class ProcessLangCopy
{


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CopyLangData  $event
     * @return void
     */
    public function handle(CopyLangData $event)
    {
        $lang = new Lang();
        $lang->copyLangData($event->sourceLang, $event->targetLang, $event->tables);
    }
}
