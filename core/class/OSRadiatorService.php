<?php

require_once __DIR__ . '/OSRadiator.class.php';
require_once __DIR__ . '/OSRadiatorCmd.php';
require_once __DIR__ . '/TasmotaDisplayService.php';

class OSRadiatorService
{
    const KEY_BACKLOG = 'backlog';
    const KEY_TEMPERATURE = 'temperature';
    const KEY_CONSIGNE = 'consigne';
    const KEY_THERMOSTAT = 'thermostat';
    const KEY_MODE = 'mode';
    const KEY_SUB_MODE = 'subMode';
    const KEY_ON_OFF = 'onOff';
    const KEY_STATE = 'state';
    const KEY_BUTTON_UP = 'buttonUp';
    const KEY_BUTTON_DOWN = 'buttonDown';
    const KEY_BUTTON_LEFT = 'buttonLeft';
    const KEY_BUTTON_RIGHT = 'buttonRight';
    const KEY_BUTTON_CLICK = 'buttonClick';

    /**
     * Get all key configuration to listen
     *
     * @return string[]
     */
    static public function getKeyConfigToListen(): array
    {
        return [
            self::KEY_TEMPERATURE,
            self::KEY_CONSIGNE,
            self::KEY_THERMOSTAT,
            self::KEY_MODE,
            self::KEY_SUB_MODE,
            self::KEY_ON_OFF,
            self::KEY_STATE,
            self::KEY_BUTTON_UP,
            self::KEY_BUTTON_DOWN,
            self::KEY_BUTTON_LEFT,
            self::KEY_BUTTON_RIGHT,
            self::KEY_BUTTON_CLICK,
        ];
    }
    /**
     * @return array[]
     */
    static public function getConfig(): array
    {
        return [
            self::KEY_BACKLOG => [
                'key' => 'screenBacklog0',
                ],
            self::KEY_TEMPERATURE => [
                'key' => 'screenTemperature',
            ],
            self::KEY_CONSIGNE => [
                'key' => 'screenConsigne',
            ],
            self::KEY_THERMOSTAT => [
                'key' => 'screenThermostat',
            ],
            self::KEY_MODE => [
                'key' => 'screenChauffageMode',
            ],
            self::KEY_SUB_MODE => [
                'key' => 'screenChauffageSousMode',
            ],
            self::KEY_ON_OFF => [
                'key' => 'screenChauffageOnOff',
            ],
            self::KEY_STATE => [
                'key' => 'screenRadiatorState',
            ],
            self::KEY_BUTTON_UP => [
                'key' => 'screenButtonUP',
            ],
            self::KEY_BUTTON_DOWN => [
                'key' => 'screenButtonDOWN',
            ],
            self::KEY_BUTTON_LEFT => [
                'key' => 'screenButtonLEFT',
            ],
            self::KEY_BUTTON_RIGHT => [
                'key' => 'screenButtonRIGHT',
            ],
            self::KEY_BUTTON_CLICK => [
                'key' => 'screenButtonCLICK',
            ],
        ];
    }

    /**
     * Get heat mode code from statut
     *
     * @param string $statut
     * @return string
     */
    static public function getHeatBrutCode(string $statut): string
    {
        $lib = [
            10 => 'Cnf',
            20 => 'Eco',
            30 => 'H-G',
            40 => 'Off',
            50 => 'C-1',
            60 => 'C-2',
        ];

        return $lib[$statut] ?? '?';
    }

    /**
     * Provide new screen with content from OSRadiator
     *
     * @param OSRadiator $eqLogic
     * @return Screen
     */
    static public function provideScreen(OSRadiator $eqLogic): Screen
    {
        $screen = new Screen();
        $screen->setOn($eqLogic->isScreenOn());

        $screenSize = ['width' => 128, 'height' => 32];
        $hasSubMode = $eqLogic->hasSubMode();

        /*
         * Components
         */
        // Heat mode
        $cHeatMode = new ScreenComponent(0, $screenSize['height']/2 - ($hasSubMode ? 8 : 4), 8);
        $cHeatMode->addScreenText($eqLogic->getScreenTextHeatMode());
        $screen->addScreenComponent($cHeatMode);
        // Heat Sub mode
        if ($hasSubMode) {
            $cHeatSubMode = new ScreenComponent(0, $screenSize['height']/2, 8);
            $cHeatSubMode->addScreenText($eqLogic->getScreenTextHeatSubMode());
            $screen->addScreenComponent($cHeatSubMode);
        }
        // Temperature/Consigne
        $cTemperature = new ScreenComponent(51, $screenSize['height']/2 - 8, 15);
        $cTemperature->addScreenText($eqLogic->getScreenTextTemperature());
        $cTemperature->addScreenText(new ScreenText(false, false, '/'));
        $cTemperature->addScreenText($eqLogic->getScreenTextTarget());
        $screen->addScreenComponent($cTemperature);
        // Heat brut
        $cHeatBrut = new ScreenComponent(51, $screenSize['height']/2, 15);
        $cHeatBrut->addScreenText($eqLogic->getScreenTextHeatBrut());
        $screen->addScreenComponent($cHeatBrut);

        // Restore selection index
        $screen->setSelectedIndex($eqLogic->getScreenSelectionIndex());

        return $screen;
    }

    /**
     * Populate screen content with text
     *
     * @param OSRadiator $eqLogic
     * @return void
     */
    static public function populateScreenContent(OSRadiator $eqLogic): void
    {
        /*
         * Values
         */
        $heatMode = $eqLogic->getConfigurationCmdValue(self::KEY_MODE);
        $heatSubMode = $eqLogic->getConfigurationCmdValue(self::KEY_SUB_MODE);
        $temperature = $eqLogic->getConfigurationCmdValue(self::KEY_TEMPERATURE);
        $consigne = $eqLogic->getConfigurationCmdValue(self::KEY_CONSIGNE);
        $heatBrut = $eqLogic->getConfigurationCmdValue(self::KEY_STATE);
        $heatOnOff = $eqLogic->getConfigurationCmdValue(self::KEY_ON_OFF);

        /*
         * Rules
         */
        if (strtolower($heatMode) == 'manuel' && strtolower($heatOnOff) == 'off') {
            $consigne = 'Off';
        }

        /*
         * Apply text
         */
        $eqLogic->getScreenTextHeatMode()->setText($heatMode);
        if ($eqLogic->hasSubMode()) {
            $eqLogic->getScreenTextHeatSubMode()->setText($heatSubMode);
        }
        $eqLogic->getScreenTextTemperature()->setText($temperature . '°C');
        $eqLogic->getScreenTextTarget()->setText($consigne . (strtolower($consigne) == 'off' ? '' : '°C'));
        $eqLogic->getScreenTextHeatBrut()->setText(self::getHeatBrutCode($heatBrut));
    }

    /**
     * Call when cmd value listened is updated
     *
     * @param array $options
     * @return void
     */
    static public function dispatchCmdListened(array $options = []): void
    {
        /** @var OSRadiator $eqLogic */
        $eqLogic = OSRadiator::byId($options['eqLogicTargetId']);
        $cmdUpdated = cmd::byId($options['event_id']);
        /** @var Screen $screen */
        $screen = $eqLogic->getScreen();

        self::logInfo($eqLogic->getHumanName() . ': Cmd ' . $cmdUpdated->getHumanName() . ' triggered.');

        /*
         * Actions (from joystick)
         */
        $isCmdUpdatedIsButton = in_array($cmdUpdated->getId(), [
            $eqLogic->getConfigurationCmd(self::KEY_BUTTON_UP)->getId(),
            $eqLogic->getConfigurationCmd(self::KEY_BUTTON_DOWN)->getId(),
            $eqLogic->getConfigurationCmd(self::KEY_BUTTON_LEFT)->getId(),
            $eqLogic->getConfigurationCmd(self::KEY_BUTTON_RIGHT)->getId(),
            $eqLogic->getConfigurationCmd(self::KEY_BUTTON_CLICK)->getId(),
        ]);

        // If is button, enable screen
        if ($isCmdUpdatedIsButton && !$eqLogic->isScreenOn()) {
            $eqLogic->setScreenOn(true);
            self::logInfo($eqLogic->getHumanName() . ": button detected, (re)enable screen for eqLogic.");
        }

        // Change selection
        switch ($cmdUpdated->getId()) {
            // UP
            case $eqLogic->getConfigurationCmd(self::KEY_BUTTON_UP)->getId():
                self::proxyToIterate($eqLogic, $cmdUpdated, 1);
                break;
            // DOWN
            case $eqLogic->getConfigurationCmd(self::KEY_BUTTON_DOWN)->getId():
                self::proxyToIterate($eqLogic, $cmdUpdated, -1);
                break;
            // LEFT
            case $eqLogic->getConfigurationCmd(self::KEY_BUTTON_LEFT)->getId():
                $screen->selectPrev();
                $eqLogic->setScreenSelectionIndex($screen->getSelectedIndex());
                break;
            // RIGHT
            case $eqLogic->getConfigurationCmd(self::KEY_BUTTON_RIGHT)->getId():
                $screen->selectNext();
                $eqLogic->setScreenSelectionIndex($screen->getSelectedIndex());
                break;
            // CLICK
            case $eqLogic->getConfigurationCmd(self::KEY_BUTTON_CLICK)->getId():
                // Add your code here if needed
                break;
        }

        // Finally, refresh screen
        self::refreshScreen($eqLogic);
    }

    public static function proxyToIterate(OSRadiator $eqLogic, cmd $cmdUpdated, int $delta = 1): void
    {
        $screen = $eqLogic->getScreen();
        $currentScreenText = $screen->getCurrentScreenText();

        switch ($currentScreenText) {
            // Target temperature
            case $eqLogic->getScreenTextTarget() :
                self::applyChangeTarget($eqLogic, $delta);
                break;
            // Heat mode
            case $eqLogic->getScreenTextHeatMode() :
                self::applyChangeItem($eqLogic, self::KEY_MODE, $delta);
                break;
            // Sub mode
            case $eqLogic->getScreenTextHeatSubMode() :
                self::applyChangeItem($eqLogic, self::KEY_SUB_MODE, $delta);
                break;
        }
    }

    /**
     * Increase or Decrease target temperature
     *
     * @param OSRadiator $eqLogic
     * @param int $delta
     * @return void
     */
    protected static function applyChangeTarget(OSRadiator $eqLogic, int $delta): void
    {
        $cmdConsigne = $eqLogic->getConfigurationCmd(self::KEY_CONSIGNE);
        $cmdThermostat = $eqLogic->getConfigurationCmd(self::KEY_THERMOSTAT);

        $consignePrevious = $cmdConsigne->execCmd();
        $thermostatPrevious = $cmdThermostat->getLastValue();

        $cmdThermostat->execCmd(['slider' => $thermostatPrevious + $delta]);

        self::logInfo($eqLogic->getHumanName() . ': apply change target. Old: ' . $consignePrevious  . ', New: ' . $cmdConsigne->execCmd());
    }

    /**
     * Change item from action list associated to cmd key
     *
     * @param OSRadiator $eqLogic
     * @param string $key
     * @param int $delta
     * @return void
     */
    protected static function applyChangeItem(OSRadiator $eqLogic, string $key, int $delta): void
    {
        $cmd = $eqLogic->getConfigurationCmd($key);
        $cmdValue = $cmd->execCmd();
        $cmdActionList = self::getCmdActionListFromCmdTarget($cmd);

        // Retrieve current index
        $currentIndex = 0;
        foreach ($cmdActionList as $index => $cmdAction) {
            if ($cmdAction->getConfiguration('value') == $cmdValue) {
                $currentIndex = $index;
                break;
            }
        }

        // New index
        $currentIndex = ($currentIndex + $delta + count($cmdActionList)) % count($cmdActionList);

        // Apply new item
        $cmdActionList[$currentIndex]->execCmd();

        self::logInfo($eqLogic->getHumanName() . ': apply change ' . $key . '. Old: ' . $cmdValue  . ', New: ' . $cmd->execCmd());
    }

    /**
     * Get all cmd action from eqLogic parent of cmdTarget
     *
     * @param cmd $cmdTarget
     * @return cmd[]
     */
    protected static function getCmdActionListFromCmdTarget(cmd $cmdTarget): array
    {
        $cmdList = [];

        // Get all cmd action from eqLogic parent of cmdTarget
        foreach ($cmdTarget->getEqLogic()->getCmd('action') as $cmd) {
            // If action target is cmdTarget, keep it
            if ($cmd->getConfiguration('infoId', null) == $cmdTarget->getId()) {
                $cmdList[] = $cmd;
            }
        }

        return $cmdList;
    }

    /**
     * Subscribe listener from all configuration cmd to listen
     *
     * @param OSRadiator $eqLogic
     * @return void
     */
    public static function subscribeListenerFromConfiguration(OSRadiator $eqLogic): void
    {
        $listenerClass = 'OSRadiator';
        $listenerFunction = 'dispatchCmdListened';

        $listenerOptions = [
            'type' => 'update',
            'eqLogicTargetId' => $eqLogic->getId(),
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
            $cmd = $eqLogic->getConfigurationCmd($key, true);
            if (!$cmd) {
                self::logDebug($eqLogic->getHumanName() . ': cmd not found for key "' . $key . '"');
                continue;
            }

            // Add event to listener
            $listener->addEvent($cmd->getId());

            // Log
            self::logInfo($eqLogic->getHumanName() . ': subscribe listener for key "' . $key . '" and cmd ' . $cmd->getHumanName());
        }

        $listener->save();
    }

    /**
     * Refresh screen
     *
     * @param OSRadiator $eqLogic
     * @return bool
     */
    public static function refreshScreen(OSRadiator $eqLogic): bool
    {
        $cmdBacklog = $eqLogic->getConfigurationCmd(OSRadiatorService::KEY_BACKLOG);
        if (!$cmdBacklog) {
            self::logError($eqLogic->getHumanName() . ': backlog cmd not found.');
            return false;
        }

        // Update content
        $screen = $eqLogic->getScreen();
        self::populateScreenContent($eqLogic);

        return TasmotaDisplayService::refresh($screen, $cmdBacklog);
    }

    static public function logDebug($message)
    {
        self::log('DEBUG', $message);
    }

    static public function logError($message)
    {
        self::log('ERROR', $message);
    }

    static public function logInfo($message)
    {
        self::log('INFO', $message);
    }

    static protected function log($level, $message): void
    {
        log::add('OSRadiator', $level, $message);
    }
}