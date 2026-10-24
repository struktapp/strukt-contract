<?php

namespace Strukt;

use Strukt\Contract\AuthInterface;
use Strukt\Contract\Http\SessionInterface;
use Strukt\Contract\UserInterface;

/**
 * Session-backed authentication gate satisfying the auth contract.
 *
 * The session store is injected rather than resolved from a global helper, so
 * the class is constructible in a test, a CLI process, or any host runtime.
 * A user already present in the session is hydrated lazily on first read,
 * which lets a fresh instance resume an authenticated session.
 */
class Auth implements AuthInterface
{
    /** Session key holding the user identifier. */
    public const SESSION_ID = 'user.id';

    /** Session key holding the login name. */
    public const SESSION_USERNAME = 'user.username';

    /** Session key holding the email address. */
    public const SESSION_EMAIL = 'user.email';

    /** Session key holding the bearer token. */
    public const SESSION_TOKEN = 'user.token';

    /** @var SessionInterface Store used to persist the session. */
    protected SessionInterface $session;

    /** @var UserInterface|null Authenticated user, or null while unresolved. */
    protected ?UserInterface $user;

    /**
     * @param SessionInterface $session Store used to persist the session.
     * @param UserInterface|null $user User to start from, or null to read the session.
     */
    public function __construct(SessionInterface $session, ?UserInterface $user = null)
    {
        $this->session = $session;
        $this->user = $user;
    }

    /**
     * Checks whether a user is authenticated.
     *
     * @return bool True when a user is present.
     */
    public function check(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Checks whether no user is authenticated.
     *
     * @return bool True when the session holds no user.
     */
    public function guest(): bool
    {
        return !$this->check();
    }

    /**
     * Reads the authenticated user, hydrating from the session when needed.
     *
     * @return UserInterface|null Authenticated user, or null for a guest.
     */
    public function user(): ?UserInterface
    {
        if ($this->user === null) {
            $this->user = $this->hydrate();
        }

        return $this->user;
    }

    /**
     * Replaces the authenticated user.
     *
     * @param UserInterface|null $user User to authenticate, or null to clear.
     * @return void
     */
    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * Reads the identifier of the authenticated user.
     *
     * @return string|null User identifier, or null for a guest.
     */
    public function id(): ?string
    {
        return $this->user()?->getId();
    }

    /**
     * Authenticates a user and persists the session.
     *
     * @param UserInterface $user User to authenticate.
     * @return bool True when the user was authenticated.
     *
     * @throws \InvalidArgumentException When the user has neither an identifier nor a username.
     */
    public function login(UserInterface $user): bool
    {
        if ($user->getId() === null && $user->getUsername() === null) {
            throw new \InvalidArgumentException('A user requires an id or a username to authenticate.');
        }

        $this->user = $user;

        $this->session->set(self::SESSION_ID, $user->getId())
            ->set(self::SESSION_USERNAME, $user->getUsername())
            ->set(self::SESSION_EMAIL, $user->getEmail())
            ->set(self::SESSION_TOKEN, $user->getToken());

        return true;
    }

    /**
     * Terminates the session of the authenticated user.
     *
     * @return void
     */
    public function logout(): void
    {
        $this->user = null;

        $this->session->remove(self::SESSION_ID)
            ->remove(self::SESSION_USERNAME)
            ->remove(self::SESSION_EMAIL)
            ->remove(self::SESSION_TOKEN);
    }

    /**
     * Rebuilds a user from the keys held in the session.
     *
     * @return UserInterface|null User from the session, or null for a guest.
     */
    protected function hydrate(): ?UserInterface
    {
        $id = $this->session->get(self::SESSION_ID);
        $username = $this->session->get(self::SESSION_USERNAME);
        $email = $this->session->get(self::SESSION_EMAIL);
        $token = $this->session->get(self::SESSION_TOKEN);

        if ($id === null && $username === null && $email === null) {
            return null;
        }

        return new User(
            id: is_string($id) ? $id : null,
            username: is_string($username) ? $username : null,
            email: is_string($email) ? $email : null,
            token: is_string($token) ? $token : null,
        );
    }
}
