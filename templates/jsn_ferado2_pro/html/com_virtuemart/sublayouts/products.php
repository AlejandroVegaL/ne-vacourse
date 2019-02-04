<?php
/**
 * sublayout products
 *
 * @package	VirtueMart
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL2, see LICENSE.php
 * @version $Id: cart.php 7682 2014-02-26 17:07:20Z Milbo $
 */

defined('_JEXEC') or die('Restricted access');
$products_per_row = $viewData['products_per_row'];
$currency = $viewData['currency'];
$showRating = $viewData['showRating'];
$verticalseparator = " vertical-separator";


$ItemidStr = '';
$Itemid = shopFunctionsF::getLastVisitedItemId();
if(!empty($Itemid)){
	$ItemidStr = '&Itemid='.$Itemid;
}


foreach ($viewData['products'] as $type => $products ) {

	$rowsHeight = shopFunctionsF::calculateProductRowsHeights($products,$currency,$products_per_row);

	if(!empty($type) and count($products)>0){
		$productTitle = vmText::_('COM_VIRTUEMART_'.strtoupper($type).'_PRODUCT'); ?>
<div class="<?php echo $type ?>-view">
  <h4 class="module-title"><span><?php echo $productTitle ?></span></h4>
		<?php // Start the Output
    }

	// Calculating Products Per Row
	$cellwidth = ' width'.floor ( 100 / $products_per_row );

	$BrowseTotalProducts = count($products);

	$col = 1;
	$nb = 1;
	$row = 1;

	foreach ( $products as $product ) {

		// Show the horizontal seperator
		if ($col == 1 && $nb > $products_per_row) { ?>
		<?php }

		// this is an indicator wether a row needs to be opened or not
		if ($col == 1) { ?>
	<div class="row">
		<?php }

		// Show the vertical seperator
		if ($nb == $products_per_row or $nb % $products_per_row == 0) {
			$show_vertical_separator = ' ';
		} else {
			$show_vertical_separator = $verticalseparator;
		}

    // Show Products ?>
	<div class="product vm-col<?php echo ' vm-col-' . $products_per_row . $show_vertical_separator ?>">
		<div class="spacer">

			<div class="vm-product-media-container">
				<div class="product-thumb">
                    <?php
                        // Check discount
                        if ($product->prices) {
                            $jsnDiscountAmount 		= $product->prices['discountAmount'];
                            if ($jsnDiscountAmount) {
                                $jsnbasePriceWithTax 	= $product->prices['basePriceWithTax'];
                                $jsnbasePrice			= $product->prices['basePrice'];

                                if($jsnbasePriceWithTax == 0) {
                                    $jsnTotal =($jsnDiscountAmount * 100) / $jsnbasePrice;
                                }else {
                                    $jsnTotal =($jsnDiscountAmount * 100) / $jsnbasePriceWithTax;
                                }
                                echo '<div class="jsn-discount-pd"><span>' . (int)$jsnTotal . '%</span></div>';
                            }
                        }
                    ?>
				<?php
					echo JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$product->virtuemart_category_id),$product->images[0]->displayMediaThumb('class="featuredProductImage" border="0"',false)); 
				?>
					<div class="p-mask">
						<h3 class="product-name">
							<?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?>
						</h3>
						<div class="star-rating">
							<?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$showRating, 'product'=>$product));?>
						</div>
						<?php if(!empty($rowsHeight[$row]['product_s_desc'])){ ?>
							<span class="p-desc">
								<?php // Product Short Description
								if (!empty($product->product_s_desc)) {
									echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 60, ' ...') ?>
								<?php } ?>
							</span>
						<?php  } ?>
						<span class="sku_wrapper">SKU: <span class="sku"><?php echo JHtml::link ($product->link, $product->product_sku); ?></span>.</span>
						<span class="posted_in">Category: <a href="<?php echo JRoute::_ ( 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $product->virtuemart_category_id , FALSE); ?>" rel="tag"><?php echo $product->category_name; ?></a>.</span>
					</div>
				</div>


				<span class="p-info">
					<!--<div class="vm-details-button">
						<?php // Product Details Button
						$link = empty($product->link)? $product->canonical:$product->link;
						//echo JHtml::link($link,vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
						//echo JHtml::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id , FALSE), vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
						?>
					</div>-->
					<?php //echo $rowsHeight[$row]['customs'] ?>
					<div class="vm3pr-<?php echo $rowsHeight[$row]['customfields'] ?>"> <?php
						echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row])); ?>
					</div>
					<?php //echo $rowsHeight[$row]['price'] ?>
					<div class="vm3pr-<?php echo $rowsHeight[$row]['price'] ?>"> <?php
						echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency)); ?>
					</div>
				</span>

			</div>
			<?php  //var_dump($product); ?>
			<?php
				if ( VmConfig::get ('display_stock', 1)) { ?>
					<!--span class="vmicon vm2-<?php echo $product->stock->stock_level ?>" title="<?php echo $product->stock->stock_tip ?>"></span -->
				<?php }
				//echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product));
			?>
		</div>
	</div>

	<?php
    $nb ++;

      // Do we need to close the current row now?
      if ($col == $products_per_row || $nb>$BrowseTotalProducts) { ?>
  </div>
      <?php
      	$col = 1;
		$row++;
    } else {
      $col ++;
    }
  }

      if(!empty($type)and count($products)>0){
        // Do we need a final closing row tag?
        //if ($col != 1) {
      ?>
    <div class="clear"></div>
  </div>
    <?php
    // }
    }
  }
