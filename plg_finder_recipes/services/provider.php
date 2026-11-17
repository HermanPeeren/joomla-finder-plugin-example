<?php

/**
 * @package     Yepr.Plugin
 * @subpackage  Finder.recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Yepr\Plugin\Finder\Recipes\Extension\Recipes;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            $container->lazy(Recipes::class, function (Container $container) {
                $plugin = new Recipes(
                    (array) PluginHelper::getPlugin('finder', 'recipes')
                );
                $plugin->setApplication(Factory::getApplication());
                $plugin->setDatabase($container->get(DatabaseInterface::class));

                return $plugin;
            })
        );
    }
};
