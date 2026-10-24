<?php

/** Coverage for the middleware pipeline contract. */

use Strukt\Contract\Http\RequestInterface;
use Strukt\Contract\Http\ResponseInterface;
use Strukt\Contract\MiddlewareInterface;
use Tests\Fixtures\Request;
use Tests\Fixtures\Response;

test('middleware runs in order and returns the response contract', function (): void {
    $order = new ArrayObject();

    $first = new class ($order) implements MiddlewareInterface {
        public function __construct(private ArrayObject $order)
        {
        }

        public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
        {
            $this->order[] = 'first:before';
            $response = $next($request, $response);
            $this->order[] = 'first:after';

            return $response;
        }
    };

    $second = new class ($order) implements MiddlewareInterface {
        public function __construct(private ArrayObject $order)
        {
        }

        public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
        {
            $this->order[] = 'second';

            return $next($request, $response);
        }
    };

    $response = pipeline(new Request('GET', '/'), new Response(), $first, $second);

    expect($order->getArrayCopy())->toBe(['first:before', 'second', 'first:after'])
        ->and($response)->toBeInstanceOf(ResponseInterface::class);
});

test('middleware can decorate the response around the inner pipeline', function (): void {
    $stamp = new class implements MiddlewareInterface {
        public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
        {
            $inner = $next($request, $response);

            return $inner->setHeader('X-Trace', 'yes');
        }
    };

    $response = pipeline(new Request('GET', '/'), new Response(), $stamp);

    expect($response->header('X-Trace'))->toBe('yes');
});

test('middleware can short-circuit without calling the rest of the chain', function (): void {
    $reached = new ArrayObject();

    $guard = new class implements MiddlewareInterface {
        public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
        {
            return $response->setStatus(401);
        }
    };

    $downstream = new class ($reached) implements MiddlewareInterface {
        public function __construct(private ArrayObject $reached)
        {
        }

        public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
        {
            $this->reached[] = true;

            return $next($request, $response);
        }
    };

    $response = pipeline(new Request('GET', '/admin'), new Response(200), $guard, $downstream);

    expect($response->status())->toBe(401)
        ->and($reached->getArrayCopy())->toBe([]);
});

test('middleware receives the bound user from the request', function (): void {
    $user = new Strukt\User(id: '7', username: 'pitsolu');

    $request = new Request('GET', '/me');
    $request->setUser($user);

    $echo = new class implements MiddlewareInterface {
        public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
        {
            return $response->setBody((string) $request->user()?->getUsername());
        }
    };

    expect(pipeline($request, new Response(), $echo)->body())->toBe('pitsolu');
});

test('the middleware contract never names a concrete response class', function (): void {
    $returnType = (new ReflectionMethod(MiddlewareInterface::class, 'process'))->getReturnType();

    expect((string) $returnType)->toBe(ResponseInterface::class);
});
