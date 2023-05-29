<?php

namespace App\Enum;

enum Status: string {
    case OK = '200';
    case BAD_REQUEST = '400';
    case NOT_FOUND = '404';
    case INTERNAL_SERVER_ERROR = '500';
}