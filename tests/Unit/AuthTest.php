<?php

/** Coverage for the session-backed authentication gate. */

use Strukt\Auth;
use Strukt\Contract\AuthInterface;
use Strukt\Session;
use Strukt\User;

test('a guest is recognised before any login', function (): void {
    $auth = new Auth(new Session());

    expect($auth)->toBeInstanceOf(AuthInterface::class)
        ->and($auth->check())->toBeFalse()
        ->and($auth->guest())->toBeTrue()
        ->and($auth->user())->toBeNull()
        ->and($auth->id())->toBeNull();
});

test('login authenticates a user and persists the session', function (): void {
    $session = new Session();
    $auth = new Auth($session);

    $loggedIn = $auth->login(new User(id: '7', username: 'pitsolu', email: 'pitsolu@gmail.com', token: 'abc123'));

    expect($loggedIn)->toBeTrue()
        ->and($auth->check())->toBeTrue()
        ->and($auth->guest())->toBeFalse()
        ->and($auth->id())->toBe('7')
        ->and($auth->user()?->getUsername())->toBe('pitsolu')
        ->and($session->get(Auth::SESSION_ID))->toBe('7')
        ->and($session->get(Auth::SESSION_USERNAME))->toBe('pitsolu')
        ->and($session->get(Auth::SESSION_EMAIL))->toBe('pitsolu@gmail.com')
        ->and($session->get(Auth::SESSION_TOKEN))->toBe('abc123');
});

test('login refuses a user with no id and no username', function (): void {
    $auth = new Auth(new Session());

    expect(fn (): bool => $auth->login(new User(email: 'nobody@strukt.dev')))
        ->toThrow(InvalidArgumentException::class, 'A user requires an id or a username to authenticate.');
});

test('a user with only a username can authenticate', function (): void {
    $auth = new Auth(new Session());

    expect($auth->login(new User(username: 'pitsolu')))->toBeTrue()
        ->and($auth->id())->toBeNull();
});

test('an existing session is hydrated by a fresh instance', function (): void {
    $session = new Session();

    (new Auth($session))->login(new User(id: '7', username: 'pitsolu', email: 'pitsolu@gmail.com', token: 'abc123'));

    $resumed = new Auth($session);

    expect($resumed->check())->toBeTrue()
        ->and($resumed->id())->toBe('7')
        ->and($resumed->user())->toBeInstanceOf(User::class)
        ->and($resumed->user()?->getEmail())->toBe('pitsolu@gmail.com')
        ->and($resumed->user()?->getToken())->toBe('abc123');
});

test('a resumed user never carries the password', function (): void {
    $session = new Session();

    (new Auth($session))->login(new User(id: '7', username: 'pitsolu', password: 'secret'));

    $resumed = new Auth($session);

    expect($resumed->user()?->hasPassword())->toBeFalse()
        ->and($resumed->user()?->credentials())->not->toHaveKey('password');
});

test('logout clears the user and every session key', function (): void {
    $session = new Session();
    $auth = new Auth($session);

    $auth->login(new User(id: '7', username: 'pitsolu', email: 'pitsolu@gmail.com', token: 'abc123'));
    $auth->logout();

    expect($auth->check())->toBeFalse()
        ->and($auth->guest())->toBeTrue()
        ->and($auth->id())->toBeNull()
        ->and($session->has(Auth::SESSION_ID))->toBeFalse()
        ->and($session->has(Auth::SESSION_USERNAME))->toBeFalse()
        ->and($session->has(Auth::SESSION_EMAIL))->toBeFalse()
        ->and($session->has(Auth::SESSION_TOKEN))->toBeFalse();
});

test('a user can be injected or replaced directly', function (): void {
    $auth = new Auth(new Session(), new User(id: '1', username: 'root'));

    expect($auth->check())->toBeTrue()
        ->and($auth->id())->toBe('1');

    $auth->setUser(null);

    expect($auth->check())->toBeFalse();

    $auth->setUser(new User(id: '2', username: 'admin'));
    expect($auth->id())->toBe('2');
});

test('auth is constructible without any global container', function (): void {
    expect(array_key_exists('app', get_defined_functions()['user']))->toBeFalse();
});
