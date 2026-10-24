<?php

/**
 * Reflection guards over the published contract surface.
 *
 * These assertions pin the shape of every contract: no contract may widen a
 * return type to a concrete class, drop nullability, or leak a credential.
 */

use Strukt\Contract\AuthInterface;
use Strukt\Contract\FormInterface;
use Strukt\Contract\Http\DownloadInterface;
use Strukt\Contract\Http\RequestInterface;
use Strukt\Contract\Http\ResponseInterface;
use Strukt\Contract\Http\SessionInterface;
use Strukt\Contract\Http\UploadedFileInterface;
use Strukt\Contract\MiddlewareInterface;
use Strukt\Contract\ProviderInterface;
use Strukt\Contract\UserInterface;

/**
 * Resolves a declared return type, preserving nullability.
 */
function returnTypeOf(string $class, string $method): string
{
    return (string) (new ReflectionMethod($class, $method))->getReturnType();
}

/**
 * Lists the methods a contract publishes.
 *
 * @return list<string> Method names.
 */
function methodsOf(string $class): array
{
    return array_map(
        static fn (ReflectionMethod $method): string => $method->getName(),
        (new ReflectionClass($class))->getMethods(),
    );
}

test('every contract is a pure interface', function (): void {
    $contracts = [
        AuthInterface::class,
        FormInterface::class,
        UserInterface::class,
        MiddlewareInterface::class,
        ProviderInterface::class,
        RequestInterface::class,
        ResponseInterface::class,
        SessionInterface::class,
        DownloadInterface::class,
        UploadedFileInterface::class,
    ];

    foreach ($contracts as $contract) {
        $reflection = new ReflectionClass($contract);

        expect($reflection->isInterface())->toBeTrue()
            ->and($reflection->getMethods())->not->toBeEmpty();
    }
});

test('the package depends on nothing at runtime', function (): void {
    $composer = json_decode((string) file_get_contents(__DIR__ . '/../../composer.json'), true);

    expect($composer['require'])->toBe(['php' => '>=8.2'])
        ->and($composer['autoload']['psr-4'])->toBe(['Strukt\\' => 'src/Strukt/'])
        ->and($composer['autoload'])->not->toHaveKey('files');
});

test('the request contract returns the user contract, not a string', function (): void {
    expect(returnTypeOf(RequestInterface::class, 'user'))->toBe('?' . UserInterface::class)
        ->and(returnTypeOf(RequestInterface::class, 'setUser'))->toBe('void')
        ->and(methodsOf(RequestInterface::class))->toContain('user');
});

test('the user contract keeps every accessor nullable', function (): void {
    foreach (['getId', 'getUsername', 'getEmail', 'getToken'] as $getter) {
        expect(returnTypeOf(UserInterface::class, $getter))->toBe('?string');
    }

    expect(returnTypeOf(UserInterface::class, 'hasPassword'))->toBe('bool')
        ->and(returnTypeOf(UserInterface::class, 'toArray'))->toBe('array');
});

test('the user contract never exposes a password getter', function (): void {
    $methods = methodsOf(UserInterface::class);

    expect($methods)->not->toContain('getPassword')
        ->and($methods)->toContain('setPassword', 'hasPassword');
});

test('fluent contracts return static so they can be chained', function (): void {
    $fluent = [
        [FormInterface::class, 'setValue'],
        [FormInterface::class, 'setValues'],
        [FormInterface::class, 'submit'],
        [FormInterface::class, 'reset'],
        [ResponseInterface::class, 'setStatus'],
        [ResponseInterface::class, 'setHeader'],
        [ResponseInterface::class, 'addHeader'],
        [ResponseInterface::class, 'removeHeader'],
        [ResponseInterface::class, 'setBody'],
        [ResponseInterface::class, 'setJson'],
        [SessionInterface::class, 'set'],
        [SessionInterface::class, 'remove'],
        [SessionInterface::class, 'clear'],
        [SessionInterface::class, 'flash'],
        [DownloadInterface::class, 'setFilename'],
        [DownloadInterface::class, 'setMime'],
        [DownloadInterface::class, 'inline'],
        [DownloadInterface::class, 'attachment'],
        [UploadedFileInterface::class, 'move'],
    ];

    foreach ($fluent as [$contract, $method]) {
        expect(returnTypeOf($contract, $method))->toBe('static')
            ->and(returnTypeOf($contract, $method))->not->toBe('void');
    }
});

test('the provider contract covers the full lifecycle', function (): void {
    $methods = methodsOf(ProviderInterface::class);

    expect($methods)->toContain('register', 'boot', 'provides')
        ->and(returnTypeOf(ProviderInterface::class, 'provides'))->toBe('array');
});

test('the form contract separates values from validation state', function (): void {
    $methods = methodsOf(FormInterface::class);

    foreach (['fields', 'rules', 'value', 'values', 'errors', 'errorsFor'] as $reader) {
        expect($methods)->toContain($reader);
    }

    expect(returnTypeOf(FormInterface::class, 'isSubmitted'))->toBe('bool')
        ->and(returnTypeOf(FormInterface::class, 'hasErrors'))->toBe('bool')
        ->and(returnTypeOf(FormInterface::class, 'validate'))->toBe('bool');
});

test('no contract references a class outside the contract namespace', function (): void {
    $contracts = [
        AuthInterface::class,
        FormInterface::class,
        UserInterface::class,
        MiddlewareInterface::class,
        ProviderInterface::class,
        RequestInterface::class,
        ResponseInterface::class,
        SessionInterface::class,
        DownloadInterface::class,
        UploadedFileInterface::class,
    ];

    foreach ($contracts as $contract) {
        foreach ((new ReflectionClass($contract))->getMethods() as $method) {
            foreach ($method->getParameters() as $parameter) {
                $type = $parameter->getType();

                if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                    continue;
                }

                expect($type->getName())->toStartWith('Strukt\Contract\\');
            }
        }
    }
});
