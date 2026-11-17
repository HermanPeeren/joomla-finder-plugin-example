<?php

/**
 * @package     Acme.Administrator
 * @subpackage  com_recipes
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Acme\Component\Recipes\Administrator\View\Recipe\HtmlView $this */
?>
<form action="<?php echo Route::_('index.php?option=com_recipes&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="recipe-form" class="form-validate">

	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general', 'recall' => true]); ?>

	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_RECIPES_TAB_RECIPE')); ?>
		<div class="row">
			<div class="col-lg-9">
				<?php echo $this->form->renderField('title'); ?>
				<?php echo $this->form->renderField('alias'); ?>
				<?php echo $this->form->renderField('description'); ?>
				<?php echo $this->form->renderField('instructions'); ?>
			</div>
			<div class="col-lg-3">
				<?php echo $this->form->renderField('image'); ?>
				<?php echo $this->form->renderField('prep_time'); ?>
				<?php echo $this->form->renderField('servings'); ?>
				<?php echo $this->form->renderField('difficulty'); ?>
				<?php echo $this->form->renderField('published'); ?>
				<?php echo $this->form->renderField('access'); ?>
				<?php echo $this->form->renderField('language'); ?>
			</div>
		</div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>

	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
		<div class="row">
			<div class="col-lg-6">
				<?php echo $this->form->renderField('metakey'); ?>
				<?php echo $this->form->renderField('metadesc'); ?>
			</div>
			<div class="col-lg-6">
				<?php echo $this->form->renderField('created'); ?>
				<?php echo $this->form->renderField('created_by'); ?>
			</div>
		</div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>

	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
