<?php

namespace Framework\Templating;

/**
 * A service that renders views as strings.
 */
interface TemplateEngineInterface {
    /**
     * Render a view with a given set of parameters.
     * @param string $view
     * @param mixed ...$params
     * @return string The rendered template.
     */
    public function render(string $view, mixed ...$params): string;
}