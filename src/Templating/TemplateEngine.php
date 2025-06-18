<?php

namespace Framework\Templating;

final class TemplateEngine implements TemplateEngineInterface
{
    private string $templatePath;

    public function __construct(string $templatePath)
    {
        $this->templatePath = rtrim($templatePath, '/');
    }

    public function render(string $view, mixed ...$params): string
    {
        $templateFile = $this->templatePath . '/' . $view;

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Template not found: $view");
        }

        $template = file_get_contents($templateFile);

        // Vervangen van tekst in html met php methodes
        $template = preg_replace(
            '/{{\s*(.+?)\s*}}/',
            '<?php echo htmlspecialchars($$1, ENT_QUOTES); ?>',
            $template
        );
        $template = preg_replace('/{% if (.+?) %}/', '<?php if ($$1): ?>', $template);
        $template = preg_replace('/{% else %}/', '<?php else: ?>', $template);
        $template = preg_replace('/{% endif %}/', '<?php endif; ?>', $template);
        $template = preg_replace('/{% for (\w+) in (\w+) %}/', '<?php foreach ($$2 as $$1): ?>', $template);
        $template = preg_replace('/{% endfor %}/', '<?php endforeach; ?>', $template);

        ob_start();
        extract($params);
        eval('?>' . $template);
        return ob_get_clean();
    }
}
