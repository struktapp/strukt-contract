<?php

namespace Strukt\Contract;

/**
 * Describes the authentication gate used to resolve the current principal.
 */
interface AuthInterface
{
    /**
     * Checks whether a user is authenticated.
     *
     * @return bool True when a user is present.
     */
    public function check(): bool;

    /**
     * Checks whether no user is authenticated.
     *
     * @return bool True when the session holds no user.
     */
    public function guest(): bool;

    /**
     * Reads the authenticated user.
     *
     * @return UserInterface|null Authenticated user, or null for a guest.
     */
    public function user(): ?UserInterface;

    /**
     * Replaces the authenticated user.
     *
     * @param UserInterface|null $user User to authenticate, or null to clear.
     * @return void
     */
    public function setUser(?UserInterface $user): void;

    /**
     * Reads the identifier of the authenticated user.
     *
     * @return string|null User identifier, or null for a guest.
     */
    public function id(): ?string;

    /**
     * Authenticates a user.
     *
     * @param UserInterface $user User to authenticate.
     * @return bool True when the user was authenticated.
     */
    public function login(UserInterface $user): bool;

    /**
     * Terminates the session of the authenticated user.
     *
     * @return void
     */
    public function logout(): void;
}
