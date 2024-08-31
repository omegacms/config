<?php
/**
 * Part of Omega CMS - Config Test Package
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
namespace Omega\Config\Tests;

/**
 * @use
 */
use PHPUnit\Framework\TestCase;
use Omega\Config\Config;
use ReflectionClass;
use ReflectionMethod;

/**
 * ConfigTest class.
 *
 * The `ConfigTest` class contains tests for the `Config` class, including  verifying 
 * the retrieval of configuration values using dot notation, handling default values, 
 * and accessing private methods through  reflection. It also  ensures that temporary 
 * configuration files are created and managed correctly during testing.
 *
 * @category    Omega
 * @package     Omega\Config\Test
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2024 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
class ConfigTest extends TestCase
{
    /**
     * Config instance.
     * 
     * @return Config $config Holds the config instance.
     */
    private Config $config;

    /**
     * Sets up the test environment by initializing the Config instance.
     *
     * This method is called before each test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Crea un'istanza della classe Config
        $this->config = new Config();
    }

    /**
     * Tests retrieval of configuration values using dot notation.
     *
     * This test verifies that the `Config` class correctly retrieves nested configuration
     * values specified using dot notation.
     *
     * @return void
     */
    public function testGetConfigValueWithDotNotation(): void
    {
        $mockConfig = [
            'database' => [
                'sqlite' => [
                    'path' => 'path/to/sqlite/database.sqlite',
                ],
                'mysql' => [
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'database' => 'test_db',
                    'username' => 'root',
                    'password' => 'password',
                ]
            ]
        ];

        // Usa la riflessione per mockare il metodo loadConfigFile
        $reflection = new ReflectionClass($this->config);
        $method = $reflection->getMethod('loadConfigFile');
        $method->setAccessible(true);
        $method->invoke($this->config, $this->createTempConfigFile($mockConfig));

        // Usa la riflessione per mockare il metodo withDots
        $method = $reflection->getMethod('withDots');
        $method->setAccessible(true);

        $value = $method->invokeArgs($this->config, [$mockConfig, ['database', 'sqlite', 'path']]);
        $this->assertSame('path/to/sqlite/database.sqlite', $value);
    }

    /**
     * Tests retrieval of configuration values with a default value.
     *
     * This test checks if the `Config` class returns the default value when the requested
     * configuration key is not found.
     *
     * @return void
     */
    public function testGetConfigValueWithDefault(): void
    {
        $mockConfig = [
            'database' => [
                'sqlite' => [
                    'path' => 'path/to/sqlite/database.sqlite',
                ],
                'mysql' => [
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'database' => 'test_db',
                    'username' => 'root',
                    'password' => 'password',
                ]
            ]
        ];

        // Usa la riflessione per mockare il metodo loadConfigFile
        $reflection = new ReflectionClass($this->config);
        $method = $reflection->getMethod('loadConfigFile');
        $method->setAccessible(true);
        $method->invoke($this->config, $this->createTempConfigFile($mockConfig));

        // Usa la riflessione per mockare il metodo withDots
        $method = $reflection->getMethod('withDots');
        $method->setAccessible(true);

        $value = $this->config->get('database.sqlite.non_existent_key', 'default_value');
        $this->assertSame('default_value', $value);
    }

    /**
     * Tests the private method `withDots` using reflection.
     *
     * This test ensures that the `withDots` method, which is used to retrieve nested configuration
     * values, works correctly. The method is accessed using reflection.
     *
     * @return void
     */
    public function testPrivateMethodWithDots(): void
    {
        $mockConfig = [
            'database' => [
                'mysql' => [
                    'host' => '127.0.0.1',
                ]
            ]
        ];

        $reflection = new ReflectionClass($this->config);
        $method = $reflection->getMethod('withDots');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->config, [$mockConfig, ['database', 'mysql', 'host']]);
        $this->assertSame('127.0.0.1', $result);

        $result = $method->invokeArgs($this->config, [$mockConfig, ['database', 'non_existent', 'host']]);
        $this->assertNull($result);
    }

    /**
     * Tests the private method `loadConfigFile` using reflection.
     *
     * This test verifies that the `loadConfigFile` method correctly loads configuration data from
     * a file. The method is accessed and tested using reflection.
     *
     * @return void
     */
    public function testPrivateMethodLoadConfigFile(): void
    {
        $mockConfig = [
            'database' => [
                'sqlite' => [
                    'path' => 'path/to/sqlite/database.sqlite',
                ],
                'mysql' => [
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'database' => 'test_db',
                    'username' => 'root',
                    'password' => 'password',
                ]
            ]
        ];

        $reflection = new ReflectionClass($this->config);
        $method = $reflection->getMethod('loadConfigFile');
        $method->setAccessible(true);

        $tempFile = $this->createTempConfigFile($mockConfig);
        $result = $method->invokeArgs($this->config, [$tempFile]);

        $this->assertSame($mockConfig, $result);

        // Pulisci il file temporaneo
        unlink($tempFile);
    }

    /**
     * Creates a temporary configuration file for testing purposes.
     *
     * This helper method creates a temporary file containing the provided configuration data,
     * which is used in the tests. The file is returned as a string representing its path.
     *
     * @param array<string, mixed> $configData The configuration data to write to the file.
     * @return string The path to the temporary configuration file.
     */
    private function createTempConfigFile(array $configData): string
    {
        $tempFile = sys_get_temp_dir() . '/test_config.php';
        file_put_contents($tempFile, '<?php return ' . var_export($configData, true) . ';');
        return $tempFile;
    }
}
