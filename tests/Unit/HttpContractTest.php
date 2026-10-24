<?php

/** Coverage for the request and response contracts. */

use Strukt\User;
use Tests\Fixtures\Request;
use Tests\Fixtures\Response;

test('a request exposes method, uri, and path', function (): void {
    $request = new Request('get', 'https://strukt.dev/users/7?sort=asc');

    expect($request->method())->toBe('GET')
        ->and($request->uri())->toBe('https://strukt.dev/users/7?sort=asc')
        ->and($request->path())->toBe('/users/7')
        ->and($request->isMethod('POST', 'GET'))->toBeTrue()
        ->and($request->isMethod('post'))->toBeFalse();
});

test('a request reads query values and falls back to a default', function (): void {
    $request = new Request('GET', '/search', ['q' => 'strukt', 'page' => 2]);

    expect($request->query('q'))->toBe('strukt')
        ->and($request->query('page'))->toBe(2)
        ->and($request->query('missing', 'none'))->toBe('none')
        ->and($request->query())->toBe(['q' => 'strukt', 'page' => 2]);
});

test('a request reads headers case-insensitively', function (): void {
    $request = new Request('POST', '/', [], ['Content-Type' => 'application/json']);

    expect($request->header('content-type'))->toBe('application/json')
        ->and($request->header('Content-Type'))->toBe('application/json')
        ->and($request->hasHeader('CONTENT-TYPE'))->toBeTrue()
        ->and($request->header('accept', 'none'))->toBe('none')
        ->and($request->hasHeader('accept'))->toBeFalse()
        ->and($request->header())->toBe(['Content-Type' => 'application/json']);
});

test('a request prefers body values over query values', function (): void {
    $request = new Request('POST', '/items?page=1', ['page' => 1], [], '{"page":3}', ['page' => 3, 'name' => 'axe']);

    expect($request->body())->toBe('{"page":3}')
        ->and($request->input('page'))->toBe(3)
        ->and($request->input('missing', 'fallback'))->toBe('fallback')
        ->and($request->all())->toBe(['page' => 3, 'name' => 'axe'])
        ->and($request->input())->toBe(['page' => 3, 'name' => 'axe']);
});

test('a request binds and clears the authenticated user', function (): void {
    $request = new Request('GET', '/me');

    expect($request->user())->toBeNull();

    $user = new User(id: '7', username: 'pitsolu');
    $request->setUser($user);

    expect($request->user())->toBe($user);

    $request->setUser();

    expect($request->user())->toBeNull();
});

test('a response tracks status, body, and headers', function (): void {
    $response = new Response();

    expect($response->status())->toBe(200)
        ->and($response->body())->toBe('');

    $response->setStatus(201)->setBody('created');

    expect($response->status())->toBe(201)
        ->and($response->body())->toBe('created');
});

test('a response rejects an out-of-range status code', function (): void {
    expect(fn () => (new Response())->setStatus(99))->toThrow(InvalidArgumentException::class);

    expect(fn () => (new Response())->setStatus(600))->toThrow(InvalidArgumentException::class);
});

test('a response replaces or appends header values', function (): void {
    $response = new Response();

    $response->setHeader('X-Trace', 'one');
    $response->addHeader('X-Trace', 'two');

    expect($response->header('X-Trace'))->toBe('one')
        ->and($response->header()['x-trace'])->toBe(['one', 'two']);

    $response->setHeader('X-Trace', 'only');

    expect($response->header('X-Trace'))->toBe('only');
});

test('a response removes headers and reports membership', function (): void {
    $response = new Response();

    $response->setHeader('X-Gone', 'yes');

    expect($response->hasHeader('X-Gone'))->toBeTrue();

    $response->removeHeader('X-Gone');

    expect($response->hasHeader('X-Gone'))->toBeFalse()
        ->and($response->header('X-Gone', 'fallback'))->toBe('fallback');
});

test('a response encodes a json body and its content type', function (): void {
    $response = (new Response())->setJson(['ok' => true, 'id' => 7]);

    expect($response->header('Content-Type'))->toBe('application/json')
        ->and($response->body())->toBe('{"ok":true,"id":7}');
});

test('a response captures what it emits on send', function (): void {
    $response = (new Response(404, 'missing'))->setHeader('Content-Type', 'text/plain');

    expect($response->sent())->toBe([]);

    $response->send();

    expect($response->sent())->toBe(['HTTP/1.1 404', 'missing']);
});

test('a response rejects an empty header name', function (): void {
    expect(fn () => (new Response())->setHeader('  ', 'x'))->toThrow(InvalidArgumentException::class);
});
