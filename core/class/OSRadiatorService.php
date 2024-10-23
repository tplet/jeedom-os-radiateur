<?php

require_once __DIR__ . '/OSRadiator.class.php';
require_once __DIR__ . '/OSRadiatorCmd.php';


class OSRadiatorService
{
    /**
     * Generate default commands for eqLogic
     *
     * @param OSRadiator $eqLogic
     * @return bool
     */
    static public function generateDefaultCommands(OSRadiator $eqLogic): bool
    {
        self::logDebug('Generate default commands for device "' . $eqLogic->getName() . '" (' . $eqLogic->getLogicalId() . ')');

        // Generate all default commands
        foreach (self::generateDefaultCommandsConfig() as $key => $config) {
            $cmd = self::generateOSRadiatorCmd(
                $eqLogic,
                $key,
                $config['name'],
                $config['type'],
                $config['unit'] ?? ''
            );

            self::logInfo('Create default info command "' . $cmd->getName() . '"');

            $cmd->save();
        }

        return true;
    }

    /**
     * @return array[]
     */
    static protected function generateDefaultCommandsConfig(): array
    {
        return [
            'mode' => [
                'name' => 'Mode',
                'type' => 'string',
            ],
            'submode' => [
                'name' => 'Sous-mode',
                'type' => 'string',
            ],
            'onoff' => [
                'name' => 'On/Off',
                'type' => 'string',
            ],
            'temperature' => [
                'name' => 'Température',
                'type' => 'numeric',
                'unit' => '°C',
            ],
            'target' => [
                'name' => 'Consigne',
                'type' => 'numeric',
                'unit' => '°C',
            ],
            'radiator-state' => [
                'name' => 'Etat radiateur',
                'type' => 'string',
            ],
            'joystick-UP' => [
                'name' => 'Joystick-UP',
                'type' => 'string',
            ],
            'joystick-DOWN' => [
                'name' => 'Joystick-DOWN',
                'type' => 'string',
            ],
            'joystick-LEFT' => [
                'name' => 'Joystick-LEFT',
                'type' => 'string',
            ],
            'joystick-RIGHT' => [
                'name' => 'Joystick-RIGHT',
                'type' => 'string',
            ],
            'joystick-CLICK' => [
                'name' => 'Joystick-CLICK',
                'type' => 'string',
            ],
        ];
    }

    static protected function generateOSRadiatorCmd(
        OSRadiator $eqLogic,
        string $logicalId,
        string $name,
        string $subtype = '',
        string $unit = ''
    ): OSRadiatorCmd
    {
        $cmd = new OSRadiatorCmd();
        $cmd->setLogicalId($logicalId);
        $cmd->setEqLogic_id($eqLogic->getId());
        $cmd->setName($name);
        $cmd->setType('info');
        $cmd->setIsVisible(true);
        $cmd->setIsHistorized(false);
        $cmd->setSubType($subtype);
        $cmd->setUnite($unit);

        return $cmd;
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