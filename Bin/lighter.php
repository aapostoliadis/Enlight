<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Tool
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * Creates a new enlight application only with name and path.
 * All the necessary files and folders are created automatically.
 * Example use:
 *
 * # php lighter.php -a MyProject -p /var/www/test
 *
 * @category   Enlight
 * @package    Enlight_Tool
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Lighter
{
    /**
     * @var Enlight_Loader
     */
    protected $loader;

    /**
     * @var Zend_Console_Getopt
     */
    protected $console;

    /**
     * @var string
     */
    protected $app;

    /**
     * @var string
     */
    protected $appPath;

    /**
     * @var array
     */
    protected $consoleRules = array(
        'app|a=s'      => 'name of the new application',
        'app_path|p=s' => 'the target directory of the application',
        'help|h|?'       => 'shows this help',
        'force|f'      => 'overwrite the existing files'
    );

    /**
     * The base project files
     *
     * @var array
     */
    protected $projectFiles = array(
        'Configs/',
        'Cache/',
        'Cache/Compiles/' => array(
            'chmod' => 0777,
        ),
        'Cache/Templates/' => array(
            'chmod' => 0777,
        ),
        'Controllers/Backend/' => array('recursive' => true),
        'Controllers/Frontend/' => array('recursive' => true),
        'Controllers/Frontend/Index.php' => array(
            'content' => '<?php
    class %app%_Controllers_Frontend_Index extends Enlight_Controller_Action
    {
        public function indexAction()
        {
            // your code here
        }
    }',
        ),
        'Views/backend/' => array('recursive' => true),
        'Views/frontend/index/' => array('recursive' => true),
        'Views/frontend/index/index.tpl' => array(
            'content' => 'Hello World',
        ),
        '.htaccess' => array(
            'content' => '<IfModule mod_rewrite.c>
    RewriteEngine On

    #RewriteBase /enlight/

    RewriteCond %{REQUEST_URI} !(Views\/|Files\/)
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [PT,L,QSA]
</IfModule>

<Files ~ "\.(tpl|yml|ini)$">
    Deny from all
</Files>

    #Options -Indexes',
        ),
        'index.php' => array(
            'content' => "<?php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/%libraryPath%'));

include_once 'Enlight/Application.php';

\$app = new Enlight_Application('production', array(
    'app' => '%app%',
    'appPath' => '.',
    'phpSettings' => array(
        'error_reporting' => E_ALL | E_STRICT,
        'display_errors' => 1,
        'date.timezone' => 'Europe/Berlin',
        'zend.ze1_compatibility_mode' => 0
    ),
    'front' => array(
        'noErrorHandler' => false,
        'throwExceptions' => true,
        'useDefaultControllerAlways' => true,
        'disableOutputBuffering' => false,
        'showException' => true,
    ),
));

return \$app->run();",
        ),
        'Bootstrap.php' => array(
            'content' => "<?php
class %app%_Bootstrap extends Enlight_Bootstrap
{
    public function run()
    {
        /** @var \$front Enlight_Controller_Front */
        \$front = \$this->getResource('Front');

        try {
            \$this->loadResource('Zend');
        } catch(Exception \$e) {
            \$front->Response()->setException(\$e);
        }

        return \$front->dispatch();
    }
}",
        ),
    );

    /**
     * Sets the include path.
     *
     * @return bool
     * @throws Exception
     */
    protected function initIncludePath()
    {
        $relativePath = dirname(__FILE__) . '/../Library/';
        if (file_exists($relativePath . 'Enlight/Loader.php')) {
            $org = set_include_path(realpath($relativePath) . PATH_SEPARATOR . get_include_path());
            if(get_include_path() === $org) {
                throw new Exception('Include path "' . $relativePath . '" could not be set failure.');
            }
        }
        return true;
    }

    /**
     * Include the auto load / tests the loading
     *
     * @return Enlight_Loader
     * @throws Exception
     */
    protected function initLoader()
    {
        @include_once('Enlight/Loader.php') ;
        if(!class_exists('Enlight_Loader')) {
            throw new Exception('Enlight loader could not be included. Please check the include path.');
        }
        $loader =  new Enlight_Loader();
        $loader->registerNamespace('Enlight', 'Enlight/');
        $loader->registerNamespace('Zend', 'Zend/');
        return $loader;
    }

    /**
     * Initializes the console options
     *
     * @return Zend_Console_Getopt
     */
    protected function initConsole()
    {
        $console = new Zend_Console_Getopt(
            $this->consoleRules,
            null,
            array(
                Zend_Console_Getopt::CONFIG_DASHDASH => false,
            )
        );
        return $console;
    }

    /**
     * Initializes the app name
     *
     * @return string
     */
    protected function initApp()
    {
        if(($app = $this->console->getOption('app')) === null) {
            return null;
        }
        $app = ucfirst($app);
        if(!preg_match('#^[a-z_]+$#i', $app)) {
            throw new Exception('Name of the application contains special characters failure.');
        }
        if($app === 'Enlight') {
            throw new Exception('Name of the application can not be enlight failure.');
        }
        return $app;
    }

    /**
     * Initializes the app path
     *
     * @return string
     */
    protected function initAppPath()
    {
        if(($appPath = $this->console->getOption('app_path')) === null) {
            $appPath = '.';
        }
        if(!is_writable($appPath)) {
            throw new Exception('Application dir "' . $appPath .'" is not writable.');
        }
        $appPath = rtrim($appPath, '\\/') . DIRECTORY_SEPARATOR;
        return $appPath;
    }

    /**
     * Starts the initialization of the components
     * @return int
     */
    public function bootstrap()
    {
        $this->initIncludePath();
        $this->loader = $this->initLoader();
        $this->console = $this->initConsole();
        $this->app = $this->initApp();
        $this->appPath = $this->initAppPath();
    }

    /**
     * Runs the lighter application
     *
     * @return int
     */
    public function run()
    {
        try {
            $this->bootstrap();
            $this->dispatch();
        } catch (Zend_Console_Getopt_Exception $e) {
            fwrite(STDERR, $e->getMessage() . PHP_EOL . PHP_EOL . $e->getUsageMessage());
            return -1;
        } catch (Exception $e) {
            fwrite(STDERR, $e->getMessage() . PHP_EOL . PHP_EOL . $this->console->getUsageMessage());
            return -1;
        }
        return 1;
    }

    /**
     * Dispatch the lighter application.
     *
     * @throws Exception
     */
    public function dispatch()
    {
        if($this->console->getOption('help')) {
            echo $this->console->getUsageMessage();
        } elseif($this->app !== null) {
            $this->createProject();
        } else {
            throw new Exception('A name for the application are required failure.');
        }
    }

    /**
     * Creates the new project
     * @throws Exception
     */
    public function createProject()
    {
        $force = !!$this->console->getOption('force');
        $realAppPath = realpath($this->appPath) . DIRECTORY_SEPARATOR;
        $realLibraryPath = realpath(dirname(__FILE__) . '/../') . DIRECTORY_SEPARATOR;

        $libraryPath = 'Library/';
        while(!file_exists($this->appPath . $libraryPath) && strpos($realAppPath, $realLibraryPath) === 0) {
           $libraryPath = '../' . $libraryPath;
        }

        foreach($this->projectFiles as $projectFile => $projectFileValue) {
            if(is_int($projectFile)) {
               $projectFile = $projectFileValue;
               $projectFileValue = array();
            }
            if(file_exists($this->appPath . $projectFile) && !$force) {
               throw new Exception('Project file "'. $projectFile . '" already exists failure.');
            }
            if(substr($projectFile, -1) === '/') {
                if(!file_exists($this->appPath . $projectFile)) {
                    mkdir($this->appPath . $projectFile, 0777, !empty($projectFileValue['recursive']));
                }
                if(isset($projectFileValue['chmod'])) {
                    $old = umask(0);
                    chmod($this->appPath . $projectFile, $projectFileValue['chmod']);
                    umask($old);
                }
            } else {
               $fileContent = isset($projectFileValue['content']) ? $projectFileValue['content'] : '';
               $fileContent = str_replace(array(
                   '%app%',
                   '%appPath%',
                   '%libraryPath%'
               ), array(
                   $this->app,
                   $this->appPath,
                   $libraryPath
               ), $fileContent);
               file_put_contents($this->appPath . $projectFile, $fileContent);
            }
        }
    }

    /**
     * main()
     *
     * @return void
     */
    public static function main()
    {
        $zf = new self();
        $zf->run();
    }
}

if (!getenv('LIGHTER_NO_MAIN')) {
    return Lighter::main();
}