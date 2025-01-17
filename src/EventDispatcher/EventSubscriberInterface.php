<?php

namespace Framework\EventDispatcher;

/**
 * A service that wishes to subscribe to one or more events.
 */
interface EventSubscriberInterface
{
    /**
     * Get a list of event handlers in this class.
     * @return array<string, string> Associative array mapping event class names to callback methods in this class.
     */
    public function getSubscribedEvents(): array;
}