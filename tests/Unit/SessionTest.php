<?php

/** Coverage for the array-backed session store and its flash semantics. */

use Strukt\Contract\Http\SessionInterface;
use Strukt\Session;

test('a session stores, reads, and reports keys', function (): void {
    $session = new Session();

    expect($session)->toBeInstanceOf(SessionInterface::class)
        ->and($session->get('missing', 'fallback'))->toBe('fallback')
        ->and($session->has('name'))->toBeFalse();

    $session->set('name', 'pitsolu')->set('role', 'admin');

    expect($session->get('name'))->toBe('pitsolu')
        ->and($session->has('name'))->toBeTrue()
        ->and($session->all())->toBe(['name' => 'pitsolu', 'role' => 'admin']);
});

test('a session distinguishes a present null from an absent key', function (): void {
    $session = new Session();

    $session->set('nullable', null);

    expect($session->has('nullable'))->toBeTrue()
        ->and($session->get('nullable', 'fallback'))->toBeNull();
});

test('a session removes one key or all of them', function (): void {
    $session = new Session(['a' => 1, 'b' => 2]);

    $session->remove('a');
    expect($session->all())->toBe(['b' => 2]);

    $session->clear();
    expect($session->all())->toBe([]);
});

test('a session preloads values through its constructor', function (): void {
    expect((new Session(['token' => 'abc']))->get('token'))->toBe('abc');
});

test('flash values are consumed on first read', function (): void {
    $session = new Session();

    $session->flash('notice', 'saved');

    expect($session->getFlash('notice'))->toBe('saved')
        ->and($session->getFlash('notice'))->toBeNull()
        ->and($session->getFlash('notice', 'fallback'))->toBe('fallback');
});

test('pulling flashes drains every pending value', function (): void {
    $session = new Session();

    $session->flash('a', 1)->flash('b', 2);

    expect($session->pullFlash())->toBe(['a' => 1, 'b' => 2])
        ->and($session->pullFlash())->toBe([]);
});
