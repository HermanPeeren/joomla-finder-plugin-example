<?php

/**
 * Smart Search result layout for recipes.
 *
 * Copy this file to
 *   templates/cassiopeia/html/com_finder/search/default_recipe.php
 *
 * com_finder picks it up because the adapter declares `protected $layout = 'recipe';`
 * If the file is absent, com_finder silently falls back to default_result.php -
 * no error, no warning, just the ordinary rendering.
 *
 * A template override cannot be installed by a plugin manifest, which is why it
 * lives here as a reference copy rather than inside plg_finder_recipes.
 *
 * Derived from com_finder's own default_result.php. The part that matters for
 * this example is the "recipe facts" list: prep_time, servings and difficulty
 * are not columns anything reads at search time. They reached this layout
 * because index() assigned them to the Result object, which keeps any property
 * it does not declare itself and stores it with the indexed link.
 *
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Finder\Administrator\Indexer\Helper;
use Joomla\String\StringHelper;

/** @var \Joomla\Component\Finder\Site\View\Search\HtmlView $this */

// At the moment results are rendered the plugin is not instantiated, so its
// $autoloadLanguage has done nothing for this request. Load the strings here.
Factory::getApplication()->getLanguage()->load('plg_finder_recipes', JPATH_ADMINISTRATOR);

$result = $this->result;

// The fields this layout exists for, read back as plain properties.
$prepTime   = (int) ($result->prep_time ?? 0);
$servings   = (int) ($result->servings ?? 0);
$difficulty = (string) ($result->difficulty ?? '');

$description = '';

if ($this->params->get('show_description', 1)) {
    $full = $result->description;

    if (!empty($result->summary) && !empty($result->body)) {
        $full = Helper::parse($result->summary . $result->body);
    }

    $description = HTMLHelper::_(
        'string.truncate',
        StringHelper::substr($full, 0),
        $this->params->get('description_length', 255),
        true
    );
}
?>
<li class="result__item result__item--recipe">
	<?php if ($this->params->get('show_image', 0) && !empty($result->imageUrl)) : ?>
		<figure class="result__image">
			<?php if ($result->route) : ?>
				<a href="<?php echo Route::_($result->route); ?>">
					<?php echo HTMLHelper::_('image', $result->imageUrl, $result->imageAlt, [], false, -1); ?>
				</a>
			<?php else : ?>
				<?php echo HTMLHelper::_('image', $result->imageUrl, $result->imageAlt, [], false, -1); ?>
			<?php endif; ?>
		</figure>
	<?php endif; ?>

	<p class="result__title">
		<?php if ($result->route) : ?>
			<?php echo HTMLHelper::link(
				Route::_($result->route),
				'<span class="result__title-text">' . $result->title . '</span>',
				['class' => 'result__title-link']
			); ?>
		<?php else : ?>
			<?php echo $result->title; ?>
		<?php endif; ?>
	</p>

	<?php if ($prepTime || $servings || $difficulty !== '') : ?>
		<ul class="result__recipe-facts">
			<?php if ($prepTime) : ?>
				<li><?php echo Text::plural('PLG_FINDER_RECIPES_PREP_TIME_N_MINUTES', $prepTime); ?></li>
			<?php endif; ?>
			<?php if ($servings) : ?>
				<li><?php echo Text::plural('PLG_FINDER_RECIPES_N_SERVINGS', $servings); ?></li>
			<?php endif; ?>
			<?php if ($difficulty !== '') : ?>
				<li>
					<?php echo Text::_('PLG_FINDER_RECIPES_DIFFICULTY'); ?>:
					<?php echo Text::_('PLG_FINDER_RECIPES_DIFFICULTY_' . strtoupper($difficulty)); ?>
				</li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>

	<?php if ($description !== '') : ?>
		<p class="result__description">
			<?php if ($result->start_date && $this->params->get('show_date', 1)) : ?>
				<time class="result__date" datetime="<?php echo HTMLHelper::_('date', $result->start_date, 'c'); ?>">
					<?php echo HTMLHelper::_('date', $result->start_date, Text::_('DATE_FORMAT_LC3')); ?>
				</time>
			<?php endif; ?>
			<?php echo $description; ?>
		</p>
	<?php endif; ?>
</li>
