<?php
/**
 * Webenq
 *
 *  LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    Webenq_Application
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

require_once 'Zend/Application.php';
require_once 'Zend/Config/Ini.php';

/**
 * @package    Webenq_Application
 */
class Webenq_Application extends Zend_Application
{
    /**
     * Path to the ini file with the default configuration, relative to APPLICATION_PATH,
     * or array containing confiuration settings, or Zend_Config object.
     *
     * @var string|array|Zend_Config
     */
    public static $defaultConfig;

    /**
     * Path to the ini file with the override configuration, relative to APPLICATION_PATH,
     * or array containing confiuration settings, or Zend_Config object.
     *
     * @var string|array|Zend_Config
     */
    public static $overrideConfig;

    /**
     * Merges the default configuration with the override configuration and instantiates
     * the application based on the environment provided.
     *
     * @param string $environment
     * @throws Exception
     */
    public function __construct($environment)
    {
        if (!self::$defaultConfig) {
            throw new Exception('Default configuration must be set before instantiating Webenq_Application');
        }

        // get default configuration
        if (is_string(self::$defaultConfig)) {
            if (!file_exists(self::$defaultConfig)) {
                throw new Exception('Default configuration file not found');
            }
            $config = new Zend_Config_Ini(
                self::$defaultConfig, null, array(
                    'allowModifications' => true)
            );
        } elseif (is_array(self::$defaultConfig)) {
            $config = new Zend_Config(self::$defaultConfig, true);
        } elseif (self::$defaultConfig instanceof Zend_Config) {
            $config = new Zend_Config(self::$defaultConfig->toArray(), true);
        } else {
            throw new Exception(
                'Webenq_Application::$defaultConfig must be a string,
                array or instance of Zend_Config'
            );
        }

        // get override configuration
        if (self::$overrideConfig) {
            if (is_string(self::$overrideConfig)) {
                if (!file_exists(self::$overrideConfig)) {
                    throw new Exception('Override configuration file not found');
                }
                $override = new Zend_Config_Ini(self::$overrideConfig);
            } elseif (is_array(self::$overrideConfig)) {
                $override = new Zend_Config(self::$overrideConfig);
            } elseif (self::$overrideConfig instanceof Zend_Config) {
                $override = self::$overrideConfig;
            } else {
                throw new Exception(
                    'Webenq_Application::$overrideConfig must be a string,
                    array or instance of Zend_Config'
                );
            }

            $config->merge($override)->setReadOnly();
        }

        if (!$config->{$environment}) {
            throw new Exception('No configuration found for application environment "' . $environment . '"');
        }

        parent::__construct($environment, $config->{$environment});
    }
}