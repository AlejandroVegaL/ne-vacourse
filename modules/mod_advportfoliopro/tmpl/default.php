<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/components/com_advportfoliopro/helpers/route.php';

JHtml::addIncludePath(JPATH_ROOT . '/administrator/components/com_advportfoliopro/helpers/html/');
JHtml::_('advportfoliopro.modal');
JHtml::_('stylesheet', 'com_advportfoliopro/style.css', array(), true);
JHtml::_('script', 'com_advportfoliopro/jquery.flexslider.js', array(), true);
JHtml::_('script', 'com_advportfoliopro/script.js', array(), true);

$document		= JFactory::getDocument();

$document->addScriptDeclaration('ExtStore.AdvPortfolioPro.live_site = \'' . JUri::base(true) . '\';');

// get style
$numColumns		= $params->get('num_columns', 1);
$imageWidth		= $params->get('image_width', 1200 / $numColumns);

$overlayColor1	= $params->get('overlay_color1', '#5aabd6');
$overlayColor2	= $params->get('overlay_color2', '');

if ($overlayColor1 == '#5aabd6' && !$overlayColor2) {
	$overlayColor2	= '#90c9e8';
}

if ($overlayColor2) {
	$css	= <<<CSS
#portfolio-module-$id .projects-wrapper .project-img .project-img-extra {
	background-image: -webkit-linear-gradient(top , $overlayColor2 0%, $overlayColor1 100%);
	background-image: -moz-linear-gradient(top , $overlayColor2 0%, $overlayColor1 100%);
	background-image: -o-linear-gradient(top , $overlayColor2 0%, $overlayColor1 100%);
	background-image: -ms-linear-gradient(top , $overlayColor2 0%, $overlayColor1 100%);
	background-image: linear-gradient(top , $overlayColor2 0%, $overlayColor1 100%);
}
CSS;

} else {
	$css	= "
#portfolio-module-$id .projects-wrapper .project-img .project-img-extra {
	background: $overlayColor1;
}
";
}

$document->addStyleDeclaration($css);

?>

<div id="portfolio-module-<?php echo $id; ?>"
	 class="portfolio-module<?php echo $moduleclass_sfx; ?>"
	 data-show-navigation="<?php echo $params->get('show_navigation', 1); ?>"
	 data-show-direction-navigation="<?php echo $params->get('show_direction_navigation', 1); ?>"
	 data-animation="<?php echo $params->get('animation', 'slide'); ?>"
	 data-speed="<?php echo $params->get('speed', 5000); ?>"
	 data-max-items="<?php echo $params->get('num_columns', 1); ?>"
	 data-item-width="<?php echo $params->get('item_width', 300); ?>"
>
	<ul class="clearfix slides projects-wrapper">
		<?php foreach ($items as $item) :
			$link			= AdvPortfolioProHelperRoute::getProjectRoute($item->slug, $item->catslug);
			$cat_link		= AdvPortfolioProHelperRoute::getCategoryRoute($item->catslug);
			$class			= '';
		?>

		<li class="project-<?php echo $item->id . $class; ?>">
			<?php if ($item->thumbnail) : ?>
				<div class="project-img">
					<?php echo JHtml::_('advportfoliopro.image', $item->thumbnail, $imageWidth, null, $item->thumbnail, false); ?>


					<?php if ($params->get('show_info', 1)) : ?>

					<div class="project-img-extra">
						<div class="project-img-extra-content">
							<?php if ($params->get('show_info_project_details', 1)) : ?>
							<a class="project-icon" href="<?php echo $link; ?>" title="<?php echo JText::_('COM_ADVPORTFOLIOPRO_DETAILS'); ?>">
								<?php echo JText::_('COM_ADVPORTFOLIOPRO_DETAILS'); ?>
							</a>
							<?php endif; ?>

							<?php if ($item->link && $params->get('show_info_project_link', 1)) : ?>
							<a class="project-icon link-icon" href="<?php echo $item->link; ?>" title="<?php echo JText::_('COM_ADVPORTFOLIOPRO_LINK'); ?>">
								<?php echo JText::_('COM_ADVPORTFOLIOPRO_LINK'); ?>
							</a>
							<?php endif; ?>

							<?php if ($params->get('show_info_project_gallery', 1)) : ?>
							<a class="project-icon gallery-icon" data-project-id="<?php echo $item->id; ?>" href="<?php echo $link; ?>" title="<?php echo JText::_('COM_ADVPORTFOLIOPRO_GALLERY'); ?>">
								<?php echo JText::_('COM_ADVPORTFOLIOPRO_GALLERY'); ?>
							</a>
							<?php endif; ?>

							<?php if ($params->get('show_info_title', 1)) : ?>
							<h4><?php echo $item->title; ?></h4>
							<?php endif; ?>

							<?php if ($params->get('show_info_category', 1)) : ?>
							<h5><a href="<?php echo $cat_link ?>"><?php echo $item->category_title; ?></a></h5>
							<?php endif; ?>
						</div>
					</div>

					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ($params->get('show_title_list', 1) || $params->get('show_category') || $params->get('show_short_description', 1)) : ?>

			<div class="project-item-meta">
				<?php if ($params->get('show_title_list', 1)) : ?>
					<h4>
						<a rel="bookmark" title="<?php echo $item->title; ?>" href="<?php echo $link; ?>">
							<?php echo $item->title; ?>
						</a>
					</h4>
				<?php endif; ?>

				<?php if ($params->get('show_category')) : ?>
					<h5>
						<a href="<?php echo $cat_link ?>">
							<?php echo $item->category_title; ?>
						</a>
					</h5>
				<?php endif; ?>

				<?php if ($params->get('show_short_description', 1)) : ?>
					<?php echo $item->short_description; ?>
				<?php endif; ?>
			</div>

			<?php endif; ?>
		</li>

		<?php endforeach; ?>
	</ul>
</div>