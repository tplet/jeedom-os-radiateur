<?php

require_once __DIR__ . '/Screen.php';
require_once __DIR__ . '/ScreenComponent.php';
require_once __DIR__ . '/ScreenText.php';


class TasmotaDisplayService
{
    static private array $lib = [
        ':' => '~3a',
        '°' => '~f8',
        'é' => '~82',
        'è' => '~8a',
        'ê' => '~88',
        '/' => '~2f',
        ' ' => '~20',
    ];

    /**
     * Encode text with compatible char for display
     */
    static public function encode($str)
    {
        return str_replace(array_keys(self::$lib), array_values(self::$lib), $str);
    }

    /**
     * Generate DisplayText part for component
     */
    static public function generateDTPart(ScreenComponent $component, bool $allowSelection = true): string
    {
        $command = '[x' . $component->getX() . 'y' . $component->getY() . ']';
        foreach ($component->getScreenTexts() as $text) {
            $command .= '[' . ($allowSelection && $text->isSelected() ? 'B1C0' : 'B0C1') . ']' . self::encode($text->getText());
        }

        return $command;
    }

    /**
     * Render display text instruction
     */
    static public function render(Screen $screen, bool $init = true, bool $clear = true): string
    {
        $message = [];
        // Init screen
        if ($init) {
            $message[] = 'DisplayDimmer 1; DisplaySize 1;';
        }

        // Prepare content
        $hasSelection = $screen->hasSelection();
        $displayText = [];
        foreach ($screen->getScreenComponents() as $component) {
            $displayText[] = self::generateDTPart($component, $hasSelection);
        }

        // Temporary: Display horizontal line
        $displayText[] = '[x0y32h128]';

        // DisplayText instruction
        $message[] = 'DisplayText [O'. ($clear ? 'z' : '') . 'C1' . ($init ? 'f0' : '') . ']' . implode('', $displayText) . ';';

        return implode(' ', $message);
    }
}
