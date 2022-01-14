<?php

namespace App;

class Session
{

    public function __construct()
    {
        $this->ensureStarted();
    }

    public function getStatus()
    {
        return session_status();
    }

    public function reset()
    {
        return session_reset();
    }

    private function ensureStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $this->ensureStarted();
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * @param  string $key
     * @param  $value
     * @return mixed
     */
    public function set(string $key, $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function delete(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }
}
