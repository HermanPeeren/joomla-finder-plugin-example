<?php

/**
 * @package     Yepr.Plugin
 * @subpackage  Finder.recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Yepr\Plugin\Finder\Recipes\Extension;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\Finder as FinderEvent;
use Joomla\Component\Finder\Administrator\Indexer\Adapter;
use Joomla\Component\Finder\Administrator\Indexer\Helper;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;
use Joomla\Component\Finder\Administrator\Indexer\Result;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\QueryInterface;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Smart Search adapter for com_recipes.
 */
final class Recipes extends Adapter implements SubscriberInterface
{
    use DatabaseAwareTrait;

    /**
     * The plugin identifier: must be unique across all finder adapters.
     */
    protected $context = 'Recipes';

    /**
     * The extension this adapter indexes.
     */
    protected $extension = 'com_recipes';

    /**
     * The com_finder sublayout used to render a result.
     */
    protected $layout = 'recipe';

    /**
     * The content type shown in the Smart Search filters. Registered automatically.
     */
    protected $type_title = 'Recipe';

    /**
     * The table holding the indexable items.
     */
    protected $table = '#__recipes';

    /**
     * Our table stores the published state in `published`, not `state`.
     */
    protected $state_field = 'published';

    protected $autoloadLanguage = true;

    /**
     * The base Adapter already subscribes to onStartIndex, onBeforeIndex,
     * onBuildIndex and onFinderGarbageCollection. We only add the events that
     * plg_content_finder re-dispatches when an item is edited.
     */
    public static function getSubscribedEvents(): array
    {
        return array_merge(parent::getSubscribedEvents(), [
            'onFinderChangeState' => 'onFinderChangeState',
            'onFinderAfterDelete' => 'onFinderAfterDelete',
            'onFinderBeforeSave'  => 'onFinderBeforeSave',
            'onFinderAfterSave'   => 'onFinderAfterSave',
        ]);
    }

    /**
     * Called once before an indexing run. Return false to abort.
     */
    protected function setup()
    {
        return true;
    }

    /**
     * Remember the access level before the save so we can detect a change.
     */
    public function onFinderBeforeSave(FinderEvent\BeforeSaveEvent $event): void
    {
        if ($event->getContext() === 'com_recipes.recipe' && !$event->getIsNew()) {
            $this->checkItemAccess($event->getItem());
        }
    }

    /**
     * Reindex the saved item, and repair the index if its access level changed.
     */
    public function onFinderAfterSave(FinderEvent\AfterSaveEvent $event): void
    {
        if ($event->getContext() !== 'com_recipes.recipe') {
            return;
        }

        $row = $event->getItem();

        if (!$event->getIsNew() && $this->old_access != $row->access) {
            $this->itemAccessChange($row);
        }

        $this->reindex($row->id);
    }

    /**
     * Drop deleted items from the index.
     */
    public function onFinderAfterDelete(FinderEvent\AfterDeleteEvent $event): void
    {
        $context = $event->getContext();
        $table   = $event->getItem();

        if ($context === 'com_recipes.recipe') {
            $this->remove($table->id);
        } elseif ($context === 'com_finder.index') {
            $this->remove($table->link_id);
        }
    }

    /**
     * Publish/unpublish/archive from the list view, and un-index everything
     * when this plugin itself is disabled.
     */
    public function onFinderChangeState(FinderEvent\AfterChangeStateEvent $event): void
    {
        $context = $event->getContext();
        $value   = $event->getValue();

        if ($context === 'com_recipes.recipe') {
            $this->itemStateChange($event->getPks(), $value);
        }

        if ($context === 'com_plugins.plugin' && $value === 0) {
            $this->pluginDisable($event->getPks());
        }
    }

    /**
     * Turn one row from getListQuery() into an index entry.
     */
    protected function index(Result $item)
    {
        if (ComponentHelper::isEnabled($this->extension) === false) {
            return;
        }

        $item->setLanguage();

        // The context the search result belongs to.
        $item->context = 'com_recipes.recipe';

        // Merge the component params with the item's own params.
        $registry     = new Registry($item->params);
        $item->params = clone ComponentHelper::getParams($this->extension, true);
        $item->params->merge($registry);

        $item->metadata = new Registry($item->metadata);

        // Run onContentPrepare over the indexable text.
        $item->summary = Helper::prepareContent($item->summary, $item->params, $item);
        $item->body    = Helper::prepareContent($item->body, $item->params, $item);

        // Stable identifier for the item; also used to find a matching menu item.
        $item->url = $this->getUrl($item->id, $this->extension, $this->layout);

        // The link the search result points at. Use your component's RouteHelper
        // here if it has one.
        $item->route = $item->url;

        // Prefer a menu item title over the raw item title, when one exists.
        $title = $this->getItemMenuTitle($item->url);

        if (!empty($title) && $this->params->get('use_menu_title', true)) {
            $item->title = $title;
        }

        // Fields that should be weighted as metadata rather than body text.
        $item->addInstruction(Indexer::META_CONTEXT, 'metakey');
        $item->addInstruction(Indexer::META_CONTEXT, 'metadesc');

        // Anything assigned to an undeclared property is stored in the Result's
        // "elements" bag, which IS serialised into #__finder_links. That is how
        // custom data reaches the result layout at search time - the row itself
        // is never queried again.
        $item->imageUrl = $item->image ?: '';
        $item->imageAlt = $item->title;

        $item->prep_time  = (int) $item->prep_time;
        $item->servings   = (int) $item->servings;
        $item->difficulty = $item->difficulty;

        // Map the component's state values onto the indexer's published state.
        $item->state = $this->translateState($item->state);

        // Facets shown in the Smart Search sidebar.
        $item->addTaxonomy('Type', 'Recipe');
        $item->addTaxonomy('Language', $item->language);

        // Pull in tags and any custom fields attached to the item.
        Helper::getContentExtras($item);
        Helper::addCustomFields($item, 'com_recipes.recipe');

        $this->indexer->index($item);
    }

    /**
     * The query that feeds the indexer. Column aliases matter: the indexer
     * expects id, title, summary, body, state, access, language, start_date
     * and (optionally) publish_start_date / publish_end_date.
     */
    protected function getListQuery($query = null)
    {
        $db = $this->getDatabase();

        return $query instanceof QueryInterface ? $query : $db->createQuery()
            ->select(
                $db->quoteName(
                    [
                        'a.id', 'a.title', 'a.alias', 'a.description', 'a.instructions',
                        'a.published', 'a.access', 'a.language', 'a.params', 'a.metadata',
                        'a.metakey', 'a.metadesc', 'a.created', 'a.modified',
                        'a.image', 'a.prep_time', 'a.servings', 'a.difficulty',
                    ],
                    [
                        null, null, null, 'summary', 'body',
                        'state', null, null, null, null,
                        null, null, 'start_date', null,
                        null, null, null, null,
                    ]
                )
            )
            ->from($db->quoteName($this->table, 'a'));
    }

    /**
     * The base implementation joins #__categories on a.catid. com_recipes has
     * no categories, so return NULLs for the category columns instead.
     */
    protected function getStateQuery()
    {
        $db = $this->getDatabase();

        return $db->createQuery()
            ->select($db->quoteName('a.id'))
            ->select($db->quoteName('a.' . $this->state_field, 'state'))
            ->select($db->quoteName('a.access'))
            ->select('NULL AS ' . $db->quoteName('cat_state'))
            ->select('NULL AS ' . $db->quoteName('cat_access'))
            ->from($db->quoteName($this->table, 'a'));
    }
}
