<?php

/** Coverage for the in-memory user record and its contract obligations. */

use Strukt\Contract\UserInterface;
use Strukt\User;

test('a user exposes every field it was built with', function (): void {
    $user = new User(id: '7', username: 'pitsolu', email: 'pitsolu@gmail.com', token: 'abc123', password: 'secret');

    expect($user)->toBeInstanceOf(UserInterface::class)
        ->and($user->getId())->toBe('7')
        ->and($user->getUsername())->toBe('pitsolu')
        ->and($user->getEmail())->toBe('pitsolu@gmail.com')
        ->and($user->getToken())->toBe('abc123');
});

test('a user defaults every field to null instead of failing', function (): void {
    $user = new User();

    expect($user->getId())->toBeNull()
        ->and($user->getUsername())->toBeNull()
        ->and($user->getEmail())->toBeNull()
        ->and($user->getToken())->toBeNull()
        ->and($user->hasPassword())->toBeFalse();
});

test('a user reports and replaces its fields through setters', function (): void {
    $user = new User();

    $user->setId('12');
    $user->setUsername('root');
    $user->setEmail('root@strukt.dev');
    $user->setToken('t0ken');

    expect($user->getId())->toBe('12')
        ->and($user->getUsername())->toBe('root')
        ->and($user->getEmail())->toBe('root@strukt.dev')
        ->and($user->getToken())->toBe('t0ken');

    $user->setUsername(null);
    $user->setEmail(null);

    expect($user->getUsername())->toBeNull()
        ->and($user->getEmail())->toBeNull();
});

test('a user withholds the password from the contract', function (): void {
    $user = new User(username: 'pitsolu', password: 'secret');

    expect($user->hasPassword())->toBeTrue()
        ->and($user->get('password', 'leaked'))->toBe('leaked')
        ->and($user->has('password'))->toBeFalse()
        ->and($user->toArray())->not->toHaveKey('password');
});

test('a user verifies hashed and plain passwords', function (): void {
    $hashed = new User(username: 'pitsolu', password: password_hash('secret', PASSWORD_BCRYPT));

    expect($hashed->verifyPassword('secret'))->toBeTrue()
        ->and($hashed->verifyPassword('wrong'))->toBeFalse();

    $plain = new User(username: 'pitsolu', password: 'secret');

    expect($plain->verifyPassword('secret'))->toBeTrue()
        ->and($plain->verifyPassword('nope'))->toBeFalse();

    expect((new User())->verifyPassword('secret'))->toBeFalse();
});

test('a user resolves fields by name through get and has', function (): void {
    $user = new User(id: '7', username: 'pitsolu');

    expect($user->get('id'))->toBe('7')
        ->and($user->get('USERNAME'))->toBe('pitsolu')
        ->and($user->get('missing', 'fallback'))->toBe('fallback')
        ->and($user->has('email'))->toBeFalse()
        ->and($user->has('username'))->toBeTrue()
        ->and(User::fields())->toBe(['id', 'username', 'email', 'token']);
});

test('a user exports public attributes and omits unset ones', function (): void {
    $user = new User(id: '7', username: 'pitsolu', token: 'abc123');

    expect($user->toArray())->toBe(['id' => '7', 'username' => 'pitsolu'])
        ->and($user->credentials())->toBe([
            'id' => '7',
            'username' => 'pitsolu',
            'token' => 'abc123',
        ]);
});
