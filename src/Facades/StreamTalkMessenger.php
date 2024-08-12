<?php

namespace streamtalk\Facades;

use Illuminate\Support\Facades\Facade;

class StreamTalkMessenger extends Facade
{

    protected static function getFacadeAccessor()
    {
       return 'StreamTalkMessenger';
    }
}
