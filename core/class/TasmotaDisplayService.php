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
        $screenTexts = $component->getScreenTexts();
        if (count($screenTexts) === 0) {
            return '';
        }

        // Pad
        $simulateLength = 0;
        foreach ($component->getScreenTexts() as $text) {
            $simulateLength += strlen($text->getText());
        }
        $padBefore = $padAfter = '';
        if ($component->hasPad()) {
            $padNb = max(0, $component->getPad() - $simulateLength);
            $padBefore = str_repeat(' ', floor($padNb / 2));
            $padAfter = str_repeat(' ', ceil($padNb / 2));
        }

        // Selected
        $selected = false;
        foreach ($screenTexts as $text) {
            if ($allowSelection && $text->isSelected()) {
                $selected = true;
                break;
            }
        }

        // Content
        $command = '[x' . $component->getX() . 'y' . $component->getY() . ($selected ? 'B1C0' : 'B0C1') . ']' . $padBefore;
        foreach ($screenTexts as $text) {
            $command .= self::encode($text->getText());
        }
        $command .= $padAfter;

        return $command;
    }

    /**
     * Render display text instruction
     */
    static public function render(Screen $screen, bool $clear = true): string
    {
        $message = [];
        // Init screen
        if (!$screen->isInitialized()) {
            $message[] = 'DisplayDimmer 1; DisplaySize 1;';
        }

        // If screen off, send off
        if (!$screen->isOn()) {
            $message[] = 'DisplayText [o];';
        }
        // Else, screen on and prepare content
        else {
            $hasSelection = $screen->hasSelection();
            $displayText = [];
            foreach ($screen->getScreenComponents() as $component) {
                $displayText[] = self::generateDTPart($component, $hasSelection);
            }

            // Temporary: Display horizontal line
            $displayText[] = '[x0y32h128]';

            // DisplayText instruction
            $message[] = 'DisplayText [O' . ($clear ? 'z' : '') . 'C1' . (!$screen->isInitialized(
                ) ? 'f0' : '') . ']' . implode('', $displayText) . ';';
        }

        return implode(' ', $message);
    }

    /**
     * Refresh screen
     *
     * @param Screen $screen
     * @param cmd $cmdBacklog
     * @return bool
     */
    static public function refresh(Screen $screen, cmd $cmdBacklog): bool
    {
        // Generate render
        $render = self::render($screen);
        if (!$screen->isInitialized()) {
            $screen->setInitialized(true);
        }

        OSRadiatorService::logDebug('Screen render: ' . $render);

        $cmdBacklog->execCmd(['message' => $render]);

        return true;
    }
}
