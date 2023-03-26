<?php

namespace App\Middleware;

/**
 * The classic CoR pattern declares a single role for objects that make up a
 * chain, which is a Handler. In our example, let's differentiate between
 * middleware and a final application's handler, which is executed when a
 * request gets through all the middleware objects.
 *
 * The base Middleware class declares an interface for linking middleware
 * objects into a chain.
 */
abstract class Middleware
{
    /**
     * @var Middleware
     */
    private $next;

    /**
     * This method can be used to build a chain of middleware objects.
     */
    public function linkWith(Middleware $next): Middleware
    {
        $this->next = $next;

        return $next;
    }

    /**
     * Subclasses must override this method to provide their own checks. A
     * subclass can fall back to the parent implementation if it can't process a
     * request.
     */
    public function check(string $email, string $password): bool
    {
        if (!$this->next) {
            return true;
        }

        return $this->next->check($email, $password);
    }
}