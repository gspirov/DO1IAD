<?php

namespace App\Enums;

enum ProjectPhaseEnum: string
{
    case DESIGN = 'design';
    case DEVELOPMENT = 'development';
    case TESTING = 'testing';
    case DEPLOYMENT = 'deployment';
    case COMPLETE = 'complete';
}
