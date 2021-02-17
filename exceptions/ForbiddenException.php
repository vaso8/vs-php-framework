<?php


namespace App\Core\Exceptions;


class ForbiddenException extends \Exception
{
    protected $message = 'Ypu don\'t have permission to access this page';
    protected $code = 403;

}