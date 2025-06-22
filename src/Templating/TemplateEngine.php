<?php

namespace Framework\Templating;

/**
 * A simple template engine that supports variable interpolation and basic control structures.
 */
final class TemplateEngine implements TemplateEngineInterface
{
    private string $templatePath;

    public function __construct(string $templatePath)
    {
        $this->templatePath = rtrim($templatePath, '/');
    }

    /**
     * Renders a template with the given parameters.
     *
     * Supports:
     * - {{ variable }} and {{ object.property }}
     * - {% if %}, {% else %}, {% endif %}
     * - {% for item in list %}, {% endfor %}
     *
     * @param string $view Template filename (relative to the template path)
     * @param mixed ...$params Variables to extract into the template
     * @return string Rendered output
     *
     * @throws \RuntimeException If the template file doesn't exist
     */
    public function render(string $view, mixed ...$params): string
    {
        $templateFile = $this->templatePath . '/' . $view;

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Template not found: $view");
        }

        $template = file_get_contents($templateFile);

        // Replace {{ object.property }}
        $template = preg_replace('/{{\s*(\w+)\.(\w+)\s*}}/', '<?php echo htmlspecialchars($\1->\2, ENT_QUOTES); ?>', $template);

        // Replace {{ variable }}
        $template = preg_replace('/{{\s*(\w+)\s*}}/', '<?php echo htmlspecialchars($\1, ENT_QUOTES); ?>', $template);

        // Replace {% if ... %}
        $template = preg_replace_callback('/{% if (.+?) %}/', function ($matches) {
            $condition = preg_replace('/\b(\w+)\b/', '\$$1', $matches[1]);
            return "<?php if ($condition): ?>";
        }, $template);

        // Replace {% else %}, {% endif %}
        $template = preg_replace('/{% else %}/', '<?php else: ?>', $template);
        $template = preg_replace('/{% endif %}/', '<?php endif; ?>', $template);

        // Replace {% for item in list %}, {% endfor %}
        $template = preg_replace('/{% for (\w+) in (\w+) %}/', '<?php foreach ($\2 as $\1): ?>', $template);
        $template = preg_replace('/{% endfor %}/', '<?php endforeach; ?>', $template);

        // Render template with extracted variables
        ob_start();
        extract($params);
        eval('?>' . $template);
        return ob_get_clean();
    }
}
