<?php
/**
 * Part of Omega CMS - Config Package
 *
 * @link       https://omegacms.github.io
 * @author     Adriano Giovannini <omegacms@outlook.com>
 * @copyright  Copyright (c) 2024 Adriano Giovannini. (https://omegacms.github.io)
 * @license    https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 */

/**
 * @declare
 */
declare( strict_types = 1 );

/**
 * @namespace
 */
namespace Omega\Config\ServiceProvider;

/**
 * @use
 */
use Omega\Application\Application;
use Omega\Config\Config;

/**
 * Config service provider class.
 *
 * The `ConfigServiceProvider` class provides a service binding for the `Config` class
 * within the Omega framework. It allows you to easily access configuration parameters
 * throughout your application.
 *
 * @category    Omega
 * @package     Omega\Config
 * @subpackage  Omega\Config\ServiceProvider
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2024 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
class ConfigServiceProvider
{
    /**
     * Bind the configuration.
     *
     * Binds an instance of the `Config` class to the application container, allowing you
     * to resolve it using the `config` key.
     *
     * @param  Application $application Holds an instance of Application.
     * @return void
     */
    public function bind( Application $application ) : void
    {
        $application->alias( 'config', function () {
            return new Config();
        });
    }
}
