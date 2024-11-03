<?php

require_once __DIR__ . '/../../../../core/php/core.inc.php';
require_once __DIR__ . '/OSRadiatorCmd.php';
require_once __DIR__ . '/OSRadiatorService.php';
require_once __DIR__ . '/TasmotaDisplayService.php';

class OSRadiator extends eqLogic
{
    const KEY_SCREEN_SELECTION_INDEX = 'screenSelectionIndex';

    /**
     * Call when cmd value listened is updated
     *
     * @param array $options
     * @return void
     */
    static public function dispatchCmdListened(array $options = []): void
    {
        OSRadiatorService::dispatchCmdListened($options);
    }

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

        $this->subscribeListenerFromConfiguration();

        // If new, generate default commands
        if ($isNew) {
            // Do when eqLogic is created
        }
    }

    /**
     * Subscribe listener from all configuration cmd to listen
     *
     * @return void
     */
    protected function subscribeListenerFromConfiguration()
    {
        $listenerClass = 'OSRadiator';
        $listenerFunction = 'dispatchCmdListened';
        $configuration = OSRadiatorService::getConfig();

        $listenerOptions = [
            'type' => 'update',
            'eqLogicTargetId' => $this->getId(),
        ];

        // Retrieve listener and create if not exists yet
        $listener = listener::byClassAndFunction($listenerClass, $listenerFunction, $listenerOptions);
        if (!is_object($listener)) {
            $listener = new listener();
            $listener->setClass($listenerClass);
            $listener->setFunction($listenerFunction);
            $listener->setOption($listenerOptions);
        }
        // Clear event top listener
        $listener->emptyEvent();

        // For each configuration commands
        foreach (OSRadiatorService::getKeyConfigToListen() as $key) {
            $configVar = $this->getConfiguration($configuration[$key]['key']);

            // If not defined, pass (eventual previous already removed)
            if (empty($configVar)) {
                continue;
            }

            // Try to find cmd target by config
            $cmd = cmd::byString($configVar);
            if (!$cmd) {
                continue;
            }

            // Add event to listener
            $listener->addEvent($cmd->getId());

            // Log
            OSRadiatorService::logInfo('Subscribe listener for eqLogic ' . $this->getHumanName() . ', key "' . $key . '" and cmd ' . $cmd->getHumanName());
        }

        $listener->save();
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