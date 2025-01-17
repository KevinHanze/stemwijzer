<?php

namespace Framework\Http;

/**
 * A service that allows access to the global session.
 */
interface SessionInterface
{
    /**
     * Store a value in the session.
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value): void;

    /**
     * Check whether the session contains a given key. This should not create a session if none existed.
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Get a value from the session. This should not create a session if none existed.
     * @param string $key
     * @return mixed The value, or null if the key was not found.
     */
    public function get(string $key): mixed;

    /**
     * Remove a key from the session.
     * @param string $key
     */
    public function unset(string $key): void;

    /**
     * Destroy the session, its cookie and its contents.
     */
    public function destroy(): void;
}