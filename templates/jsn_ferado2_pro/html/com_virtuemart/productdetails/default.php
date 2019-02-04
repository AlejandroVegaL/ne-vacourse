<?php
$vm_override_functions = JPATH_THEMES.DS.JFactory::getApplication()->getTemplate().DS."html/com_virtuemart/vm_functions.php";
require_once($vm_override_functions);
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz, Max Galt
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 8171 2014-08-12 12:17:30Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
/* Let's see if we found the product */
if (empty($this->product)) {
	echo vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
	echo '<br /><br />  ' . $this->continue_link_html;
	return;
}

echo shopFunctionsF::renderVmSubLayout('askrecomjs',array('product'=>$this->product));

vmJsApi::jDynUpdate();
$document = JFactory::getDocument();
$document->addScriptDeclaration("
//<![CDATA[
// GALT: Start listening for dynamic content update.
jQuery(document).ready(function() {
	// If template is aware of dynamic update and provided a variable let's
	// set-up the event listeners.
	if (Virtuemart.container)
		Virtuemart.updateDynamicUpdateListeners();
});
//]]>
");

if(vRequest::getInt('print',false)){ ?>
<body onload="javascript:print();">
<?php } ?>

<div class="productdetails-view productdetails">

	<div class="back-category">
	<?php // Back To Category Button
	if ($this->product->virtuemart_category_id) {
		$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
		$categoryName = $this->product->category_name ;
	} else {
		$catURL =  JRoute::_('index.php?option=com_virtuemart');
		$categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME') ;
	}
	?>
	<div class="back-to-category">
    	<a href="<?php echo $catURL ?>" class="product-details" title="<?php echo $categoryName ?>"><?php echo vmText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a>
	</div>


    <?php // afterDisplayTitle Event
    echo $this->product->event->afterDisplayTitle ?>

    <?php
    // Product Edit Link
    echo $this->edit_link;
    // Product Edit Link END
    ?>

    <?php
    // PDF - Print - Email Icon
    if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_icon')) {
	?>
        <div class="icons">
	    <?php

	    $link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;

		echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_icon', false);
	    //echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
		echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon',false,true,false,'class="printModal"');
		$MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';
	    echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend', false,true,false,'class="recommened-to-friend"');
	    ?>
    	<div class="clear"></div>
        </div>
    <?php } // PDF - Print - Email Icon END
    ?>
    <?php
	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'ontop'));
    ?>
    <div class="clear"></div>
	</div>
    <div class="vm-product-container">
			<div class="vm-product-media-container">
				<?php
				echo $this->loadTemplate('images');
				?>
				<?php
				$count_images = count ($this->product->images);
					if ($count_images > 1) {
						echo $this->loadTemplate('images_additional');
					}
				?>
			</div>
			
		<div class="vm-product-details-container">
			 <?php // Product Title   ?>
			    <h1 class="product-title"><?php echo $this->product->product_name ?></h1>
			<?php // Product Title END   ?>
		    <div class="spacer-buy-area">

			<?php
			// TODO in Multi-Vendor not needed at the moment and just would lead to confusion
			/* $link = JRoute::_('index2.php?option=com_virtuemart&view=virtuemart&task=vendorinfo&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
			  $text = vmText::_('COM_VIRTUEMART_VENDOR_FORM_INFO_LBL');
			  echo '<span class="bold">'. vmText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL'). '</span>'; ?><a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a><br />
			 */
			?>

			<?php
			echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$this->showRating,'product'=>$this->product));
			echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency));
			?>
			<div class="product_meta">
			    <?php
					// Manufacturer of the Product
					if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
					    echo '<span class="jsn-manufacturer">'.$this->loadTemplate('manufacturer').'</span>';
					}
					//var_dump($this->product->category_name);
				?>
				<span class="sku_wrapper"><b>SKU:</b> <span class="sku" ><?php echo $this->product->product_sku; ?></span></span>
				<span class="posted_in"><b>Category:</b> <?php echo $this->product->category_name; ?>.</span>
				
			</div>
			<?php

			if (is_array($this->productDisplayShipments)) {
			    foreach ($this->productDisplayShipments as $productDisplayShipment) {
				echo $productDisplayShipment;
			    }
			}
			if (is_array($this->productDisplayPayments)) {
			    foreach ($this->productDisplayPayments as $productDisplayPayment) {
				echo $productDisplayPayment;
			    }
			}

			//In case you are not happy using everywhere the same price display fromat, just create your own layout
			//in override /html/fields and use as first parameter the name of your file
		    // Product Short Description
		    if (!empty($this->product->product_s_desc)) {
			?>
		        <div class="product-short-description">
			    <?php
			    /** @todo Test if content plugins modify the product description */
			    echo nl2br($this->product->product_s_desc);
			    ?>
		        </div>
			<?php
		    } // Product Short Description END

		    // ferado_colors
			echo '<div class="prd-field-size">';
			echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'ferado_size'));
			echo '</div>';

			// ferado_colors
			echo '<div class="prd-field-color">';
			echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'ferado_colors'));
			echo '</div>';

			echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product));


			echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$this->product));

			// Ask a question about this product
			if (VmConfig::get('ask_question', 0) == 1) {
				$askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component', FALSE);
				?>
				<div class="ask-a-question">
					<a class="ask-a-question" href="<?php echo $askquestion_url ?>" rel="nofollow" ><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
				</div>
			<?php
			}
			?>
		    </div>
		    
		</div>
		<div class="clear"></div>
    </div>
	<?php echo $this->product->event->beforeDisplayContent; ?>
	<div class="information_tabs">
		<ul class="tabs">
			<li class="description_tab"><a href="#tab-description"><span><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></span></a></li>
			<li class="reviews_tab"><a href="#tab-reviews"><span><?php echo vmText::_ ('COM_VIRTUEMART_REVIEWS') ?></span></a></li>
		</ul>
	    <div class="product-description tab-content" id="tab-description">
	    <?php
		// Product Description
		if (!empty($this->product->product_desc)) {
			echo $this->product->product_desc; 
		}
		?>
	    </div>
	    <?php
		echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'normal'));

	    // Product Packaging
	    $product_packaging = '';
	    if ($this->product->product_box) {
		?>
	        <div class="product-box">
		    <?php
		        echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
		    ?>
	        </div>
	    <?php } // Product Packaging END ?>

	    <?php 
		echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'onbot'));

  		echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_products','class'=> 'product-related-products','customTitle' => true ));

		echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_categories','class'=> 'product-related-categories'));
		?>

		<?php // onContentAfterDisplay event
		echo $this->product->event->afterDisplayContent; ?>

		<?php
		echo $this->loadTemplate('reviews');
		?>
	</div>
<?php // Show child categories
    if (VmConfig::get('showCategory', 1)) {
		echo $this->loadTemplate('showcategory');
    }?>
	<?php
	if (checkVMVersion()) echo vmJsApi::writeJS();
	?>
</div>
<script>
	// GALT
	/*
	 * Notice for Template Developers!
	 * Templates must set a Virtuemart.container variable as it takes part in
	 * dynamic content update.
	 * This variable points to a topmost element that holds other content.
	 */
	// If this <script> block goes right after the element itself there is no
	// need in ready() handler, which is much better.
	//jQuery(document).ready(function() {
	Virtuemart.container = jQuery('.productdetails-view');
	Virtuemart.containerSelector = '.productdetails-view';
	//});

	jQuery(document).ready(function() {

        jQuery(".tab-content").not(':first').css("display", "none");
        jQuery("ul.tabs li:first").addClass("active");

	    jQuery("ul.tabs a").click(function(event) {
	        event.preventDefault();
	        jQuery(this).parent().addClass("active");
	        jQuery(this).parent().siblings().removeClass("active");
	        var tab = jQuery(this).attr("href");
	        jQuery(".tab-content").not(tab).css("display", "none");
	        jQuery(tab).fadeIn();
	    });
	});

</script>
