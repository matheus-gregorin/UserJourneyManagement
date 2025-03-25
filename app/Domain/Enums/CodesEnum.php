<?php

namespace App\Domain\Enums;

enum CodesEnum
{
    // Status
    public const statusSuccess = 'success';
    public const statusError = 'error';

    // Messages success
    public const messageUserAuthenticated = 'user authenticated';
    public const messageUserCreated = 'user created';
    public const messageUserNotCreated = 'user not created';
    public const messageUsersCollected = 'users collected';
    public const messageRoleUpdated = 'role updated';

    // Messages errors
    public const messageUserNotAuthenticated = 'user not authenticated';
    public const messageUserInvalid = 'user invalid';
    public const messageCredentialsInvalid = 'credentials invalid';
    public const messageDataInvalid = 'data invalid';
    public const messageInternalServerError = 'server error';
    public const messageValidationError = 'Validation error';
    public const messageUsersNotContent = 'not content users';
    public const messageNotUpdatedRole = 'not updated role';

    // Codes
    public const codeSuccess = 200;
    public const codeNotContent = 204;
    public const codeErrorBadRequest = 400;
    public const codeErrorUnauthorized = 401;
    public const codeErrorForbidden = 403;
    public const codeErrorInternalServerError = 503;
    public const codeErrorUnprocessableEntity = 422;
}
