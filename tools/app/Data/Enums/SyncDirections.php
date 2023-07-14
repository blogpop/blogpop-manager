<?php

namespace App\Data\Enums;
enum SyncDirections : string
{
    case UPLOAD = 'upload';
    case NO_ACTION = 'no_action';
    case DOWNLOAD = 'download';
}
