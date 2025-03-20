<?php

namespace App\Domain\Enums;

enum ErrorsEnum
{
    // Status
    public const statusSuccess = 'success';
    public const statusError = 'error';

    // Codes
    public const codeSuccess = 200;
    public const codeErrorBadRequest = 400;
    public const codeErrorUnauthorized = 401;
    public const codeErrorForbidden = 403;
    public const codeErrorInternalServerError = 503;

    //Messages
    public const messageUserAuthenticated = 'user authenticated';
    public const messageUserNotAuthenticated = 'user not authenticated';
    public const messageCredentialsInvalid = 'credentials invalid';
    public const messageInternalServerError = 'server error';
}
