<?php


namespace App\Core\Exceptions;


class MissingPageException extends \Exception
{
    protected $code = 404;
    protected $message = 'Page not found';
}