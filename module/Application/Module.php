<?php
/**
 * This source file is part of GotCms.
 *
 * GotCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GotCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with GotCms. If not, see <http://www.gnu.org/licenses/lgpl-3.0.html>.
 *
 * PHP Version >=5.3
 *
 * @category   Gc_Application
 * @package    Application
 * @subpackage Module
 * @author     Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license    GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.html
 * @link       http://www.got-cms.com
 */
namespace Application;

use Gc\Mvc;
use Gc\Core\Config as CoreConfig;
use Gc\Layout;
use Gc\Registry;
use Gc\Session\SaveHandler\DbTableGateway as SessionTableGateway;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\I18n\Translator;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container as SessionContainer;
use Zend\Session\SaveHandler\DbTableGatewayOptions;
use Zend\Validator\AbstractValidator;
use Zend\Uri\Http as Uri;

/**
 * Application module
 *
 * @category   Gc_Application
 * @package    Application
 * @subpackage Module
 */
class Module extends Mvc\Module
{
    /**
     * Module directory path
     */
    protected $directory = __DIR__;

    /**
     * Module namespace
     */
    protected $namespace = __NAMESPACE__;

    /**
     * On boostrap event
     *
     * @param EventInterface $event Event
     *
     * @return void
     */
    public function onBootstrap(EventInterface $event)
    {
        $application    = $event->getApplication();
        $config         = $application->getConfig();
        $serviceManager = $application->getServiceManager();

        if (isset($config['db'])) {
            $dbAdapter = $this->initDatabase($config);
            $this->initTranslator($serviceManager);
            $this->initSession($serviceManager, $dbAdapter);
            $serviceManager->get('CustomModules');

            $application->getEventManager()->attach(
                MvcEvent::EVENT_ROUTE,
                array($this, 'checkSsl'),
                -10
            );

            $application->getEventManager()->attach(
                MvcEvent::EVENT_RENDER_ERROR,
                array($this, 'prepareException'),
                10
            );

            if ($serviceManager->get('CoreConfig')->getValue('debug_is_active')) {
                $viewManager = $serviceManager->get('ViewManager');
                $viewManager->getRouteNotFoundStrategy()->setDisplayExceptions(true);
                $viewManager->getRouteNotFoundStrategy()->setDisplayNotFoundReason(true);
                $viewManager->getExceptionStrategy()->setDisplayExceptions(true);
                $viewManager->getViewModel()->debugIsActive = true;
            }
        }
    }

    /**
     * Initialize database
     *
     * @param array $config Configuration
     *
     * @return void
     */
    public function initDatabase(array $config)
    {
        $dbAdapter = new DbAdapter($config['db']);
        GlobalAdapterFeature::setStaticAdapter($dbAdapter);

        return $dbAdapter;
    }

    /**
     * Initialize Session data
     *
     * @param ServiceManager $serviceManager Service manager
     * @param DbAdapter      $dbAdapter      Database adapter
     *
     * @return void
     */
    public function initSession(ServiceManager $serviceManager, DbAdapter $dbAdapter)
    {
        $coreConfig    = $serviceManager->get('CoreConfig');
        $sessionConfig = new SessionConfig();
        $sessionConfig->setStorageOption('gc_probability', 1);
        $sessionConfig->setStorageOption('gc_divisor', 1);
        $sessionConfig->setStorageOption('save_path', $coreConfig->getValue('session_path'));
        $sessionConfig->setStorageOption('gc_maxlifetime', $coreConfig->getValue('session_lifetime'));
        $sessionConfig->setStorageOption('cookie_path', $coreConfig->getValue('cookie_path'));
        $sessionConfig->setStorageOption('cookie_domain', $coreConfig->getValue('cookie_domain'));
        SessionContainer::setDefaultManager(new SessionManager($sessionConfig));

        if ($coreConfig->getValue('session_handler') == CoreConfig::SESSION_DATABASE) {
            $tablegatewayConfig = new DbTableGatewayOptions(
                array(
                    'idColumn'   => 'id',
                    'nameColumn' => 'name',
                    'modifiedColumn' => 'updated_at',
                    'lifetimeColumn' => 'lifetime',
                    'dataColumn' => 'data',
                )
            );

            $sessionTable = new SessionTableGateway(
                new TableGateway('core_session', $dbAdapter),
                $tablegatewayConfig
            );

            $sessionManager = SessionContainer::getDefaultManager();
            $sessionManager->setSaveHandler($sessionTable)->start();
        }
    }

    /**
     * Initialize translator data
     *
     * @param ServiceManager $serviceManager Service manager
     *
     * @return void
     */
    public function initTranslator(ServiceManager $serviceManager)
    {
        $coreConfig = $serviceManager->get('CoreConfig');
        $translator = $serviceManager->get('MvcTranslator');
        $locale     = $coreConfig->getValue('locale');
        if (!empty($locale)) {
            $translator->setLocale($locale);
        }

        AbstractValidator::setDefaultTranslator($translator);
    }

    /**
     * Initialize Render error event
     *
     * @param Event $event Event
     *
     * @return void
     */
    public function prepareException($event)
    {
        if ($event->getApplication()->getMvcEvent()->getRouteMatch()->getMatchedRouteName() === 'cms') {
            $coreConfig = $event->getApplication()->getServiceManager()->get('CoreConfig');
            $layout     = Layout\Model::fromId($coreConfig->getValue('site_exception_layout'));
            if (!empty($layout)) {
                $templatePathStack = $event->getApplication()->getServiceManager()->get(
                    'Zend\View\Resolver\TemplatePathStack'
                );

                $event->getViewModel()->setTemplate('layout/' . $layout->getIdentifier());
                if ($coreConfig->getValue('stream_wrapper_is_active')) {
                    $templatePathStack->setUseStreamWrapper(true);
                    file_put_contents('zend.view://layout/' . $layout->getIdentifier(), $layout->getContent());
                }
            }
        }
    }

    /**
     * Check if ssl is forced or not
     *
     * @param EventInterface $event Mvc event
     *
     * @return null|Zend\Http\PhpEnvironment\Response
     */
    public function checkSsl(EventInterface $event)
    {
        $coreConfig       = $event->getApplication()->getServiceManager()->get('CoreConfig');
        $matchedRouteName = $event->getRouteMatch()->getMatchedRouteName();
        $request          = $event->getRequest();
        $uri              = $request->getUri();

        if ($matchedRouteName === 'cms') {
            if ($uri->getScheme() === 'https' or $coreConfig->getValue('force_frontend_ssl')) {
                $newUri = new Uri($coreConfig->getValue('secure_frontend_base_path'));
                $newUri->setScheme('https');
            } else {
                $newUri = new Uri($coreConfig->getValue('unsecure_frontend_base_path'));
            }
        } else {
            if ($uri->getScheme() === 'https' or $coreConfig->getValue('force_backend_ssl')) {
                $newUri = new Uri($coreConfig->getValue('secure_backend_base_path'));
                $newUri->setScheme('https');
            } else {
                $newUri = new Uri($coreConfig->getValue('unsecure_backend_base_path'));
            }
        }

        if (!empty($newUri) and $newUri->isValid() and
            ($newUri->getHost() != '' and $uri->getHost() != $newUri->getHost()) or
            ($newUri->getScheme() != '' and $uri->getScheme() != $newUri->getScheme())
        ) {
            $uri->setPort($newUri->getPort());
            if ($newUri->getHost() != '') {
                $uri->setHost($newUri->getHost());
            }

            if ($newUri->getScheme() != '') {
                $uri->setScheme($newUri->getScheme());
            }

            $response = $event->getResponse();
            $response->setStatusCode(302);
            $response->getHeaders()->addHeaderLine('Location', $request->getUri());
            $event->stopPropagation();

            return $response;
        }
    }
}
