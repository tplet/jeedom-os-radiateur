<?php

require_once __DIR__ . '/../../../../core/php/core.inc.php';
require_once __DIR__ . '/OSRadiatorCmd.php';
require_once __DIR__ . '/OSRadiatorService.php';
require_once __DIR__ . '/TasmotaDisplayService.php';

class OSRadiator extends eqLogic
{
    const KEY_SCREEN_SELECTION_INDEX = 'screenSelectionIndex';

    /**
     * Save eqLogic
     *
     * @param $_direct
     * @return void
     * @throws Exception
     */
    public function save($_direct = false)
    {
        $isNew = $this->getId() == '';
        parent::save($_direct);

        // If new, generate default commands
        if ($isNew) {
            OSRadiatorService::generateDefaultCommands($this);
        }
    }

    public function getScreenSelectionIndex(): int
    {
        return (int)$this->getConfiguration(self::KEY_SCREEN_SELECTION_INDEX, 0);
    }

    public function setScreenSelectionIndex(int $index): void
    {
        $this->setConfiguration(self::KEY_SCREEN_SELECTION_INDEX, $index);
    }
}