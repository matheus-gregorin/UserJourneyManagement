<?php

namespace Domain\Enums;

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
    public const automationDispatchSuccess = 'automation dispatch success';

    // Messages errors
    public const messageUserNotAuthenticated = 'user not authenticated';
    public const messageUserInvalid = 'user invalid';
    public const messageCredentialsInvalid = 'credentials invalid';
    public const messageDataInvalid = 'data invalid';
    public const messageInternalServerError = 'server error';
    public const messageValidationError = 'Validation error';
    public const messageUsersNotContent = 'not content users';
    public const messageNotUpdatedRole = 'not updated role';
    public const messageErrorUserNotContainScope = 'user not contain scope';
    public const messageErrorUserRestartScope = 'Error in restart user with scope';
    public const automationDispatchError = 'automation dispatch error';
    public const messageErrorGetUserByPhone  = 'Error in get user by phone number';
    public const messageErrorReceiveMessage  = 'Error in webhook receive message';

    // Codes
    public const codeSuccess = 200;
    public const codeNotContent = 204;
    public const codeErrorBadRequest = 400;
    public const codeErrorUnauthorized = 401;
    public const codeErrorForbidden = 403;
    public const codeErrorInternalServerError = 503;
    public const codeErrorUnprocessableEntity = 422;
}
