# Strukt Contract

Zero-dependency interfaces for HTTP, authentication, forms, and service
providers, plus small reference implementations of the user, session, and auth
contracts.

This is the foundational Strukt package: it requires nothing but PHP, so any
other package or application can depend on it without pulling a runtime.

## Install

```bash
composer require strukt/contract
```

## Contracts

| Contract | Purpose |
| --- | --- |
| `Strukt\Contract\UserInterface` | An authenticated principal. Nullable throughout, password is write-only. |
| `Strukt\Contract\AuthInterface` | The gate that resolves the current principal. |
| `Strukt\Contract\MiddlewareInterface` | One link in the HTTP pipeline. |
| `Strukt\Contract\FormInterface` | Field definitions, submitted values, and validation state. |
| `Strukt\Contract\ProviderInterface` | Container bindings contributed by a service provider. |
| `Strukt\Contract\Http\RequestInterface` | An inbound request: method, uri, headers, params, files, user. |
| `Strukt\Contract\Http\ResponseInterface` | A response under construction: status, headers, body, send. |
| `Strukt\Contract\Http\SessionInterface` | Per-client state between requests, including flash data. |
| `Strukt\Contract\Http\DownloadInterface` | A payload offered as an attachment or inline. |
| `Strukt\Contract\Http\UploadedFileInterface` | A file received through a multipart form. |

Two design rules hold across all of them:

- **No contract names a concrete class.** A middleware returns a
  `ResponseInterface`, never a specific response type, so implementations stay
  substitutable.
- **Every accessor is honest about null.** A guest is a valid state, so user
  and request accessors return `?T` rather than throwing or lying.

## Implementations

`Strukt\User`, `Strukt\Session`, and `Strukt\Auth` are dependency-free
reference implementations. They are the default wiring, not the only option:
any type satisfying the contracts can be injected.

### User

```php
use Strukt\User;

$user = new User(id: '7', username: 'pitsolu', email: 'pitsolu@gmail.com');

$user->getUsername();          // 'pitsolu'
$user->get('id');              // '7'
$user->has('email');           // true
$user->toArray();              // ['id' => '7', 'username' => 'pitsolu', 'email' => ...]
```

The password is write-only. It is never returned by a getter, is not
addressable through `get()`, and never appears in `toArray()`:

```php
$user = new User(password: password_hash('secret', PASSWORD_BCRYPT));

$user->hasPassword();          // true
$user->verifyPassword('secret'); // true
$user->verifyPassword('wrong');  // false
$user->get('password', 'leaked'); // 'leaked' — never the real value
```

Use `credentials()` when the bearer token is genuinely required, and treat the
result as sensitive.

### Session

```php
use Strukt\Session;

$session = new Session();

$session->set('theme', 'dark');
$session->get('theme');        // 'dark'
$session->has('theme');        // true

$session->flash('notice', 'saved');
$session->getFlash('notice');  // 'saved' — consumed on read
```

`has()` reports key presence, so a key holding `null` is still present:

```php
$session->set('nullable', null);

$session->has('nullable');                   // true
$session->get('nullable', 'fallback');       // null
```

### Auth

`Auth` receives its session by injection rather than resolving a global
container, so it is constructible anywhere — a test, a CLI process, or a host
runtime.

```php
use Strukt\Auth;
use Strukt\Session;
use Strukt\User;

$auth = new Auth(new Session());

$auth->guest();                 // true
$auth->login(new User(id: '7', username: 'pitsolu'));
$auth->check();                 // true
$auth->id();                    // '7'
$auth->logout();                // clears the user and the session keys
```

A fresh instance resumes an existing session, so the principal survives
across requests:

```php
$auth->login(new User(id: '7', username: 'pitsolu'));

(new Auth($session))->user()?->getUsername();  // 'pitsolu'
```

`login()` rejects a user with neither an id nor a username, because such a
record cannot be attributed to a session.

### Middleware

```php
use Strukt\Contract\Http\RequestInterface;
use Strukt\Contract\Http\ResponseInterface;
use Strukt\Contract\MiddlewareInterface;

final class Authenticate implements MiddlewareInterface
{
    public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        if ($request->user() === null) {
            return $response->setStatus(401);
        }

        return $next($request, $response);
    }
}
```

Returning without calling `$next` short-circuits the chain, which is how a
middleware rejects a request before it reaches the handler.

## Tests

```bash
composer test
composer test:coverage
```

The suite covers the implementations and asserts the published contract
surface through reflection, so a contract cannot silently widen a return type,
drop nullability, or leak a credential without a test failing.

## License

MIT
