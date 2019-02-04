<div class="row">
<div class="col-md-9 col-sm-12">
	<table class="tdborder redshop-view-cart" style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
		<thead>
		<tr>
			<th>{product_name_lbl}</th>
			<th><br/></th>
			<th class="price-exlc">{product_price_excl_lbl}</th>
			<th>{quantity_lbl}</th>
			<th class="total-price">{total_price_exe_lbl}</th>
		</tr>
		</thead>
		<tbody>
		<!-- {product_loop_start} -->
		<div class="category_print">{attribute_price_with_vat}</div>
		<tr class="tdborder">
			<td>
				<div class="cartproducttitle">{product_name}</div>
				<div class="cartattribut">{product_attribute}</div>
				<div class="cartaccessory">{product_accessory}</div>
				<div class="cartwrapper">{product_wrapper}</div>
				<div class="cartuserfields">{product_userfields}</div>
				<div>{attribute_change}</div>
			</td>
			<td>{product_thumb_image}</td>
			<td>{product_price_excl_vat}</td>
			<td>
				{update_cart}
			</td>
			<td class="td-total-cart">{product_total_price_excl_vat}{remove_product}</td>
		</tr>
		<!-- {product_loop_end} -->
		</tbody>
	</table>
	<div style="margin-top: 20px;">
		<div style="float: left;"> {shop_more}</div>
		<div style="float:right; margin-right: 0;">{empty_cart}</div>
		<div style="float:right; margin-right: 15px;">{update}</div>
	</div>
</div>
<div class="col-md-3 col-sm-12">
	<!-- gifcode -->
	<div class="discount-code">
		{discount_form_lbl}{coupon_code_lbl}
		<div>{discount_form}</div>
	</div>
	<!-- end gifcode -->
	<!-- total cart -->
	<div class="total-cart">
		<div class="title">CART</div>
		<table class="cart_calculations" border="0" width="100%">
			<tbody>
			<tr class="tdborder">
				<td>{product_subtotal_excl_vat_lbl}:</td>
				<td width="100" style="text-align: right;">{product_subtotal_excl_vat}</td>
			</tr>
			<!-- {if discount}-->
			<tr class="tdborder">
				<td>{discount_lbl}</td>
				<td width="100" style="text-align: right;">{discount}</td>
			</tr>
			<!-- {discount end if} -->
			<tr>
				<td>{shipping_with_vat_lbl}:</td>
				<td width="100" style="text-align: right;">{shipping_excl_vat}</td>
			</tr>
			<!-- {if vat} -->
			<tr>
				<td>{vat_lbl}</td>
				<td width="100" style="text-align: right;">{tax}</td>
			</tr>
			<!-- {vat end if} -->
			<!-- {if payment_discount}-->
			<tr>
				<td>{payment_discount_lbl}</td>
				<td width="100" style="text-align: right;">{payment_order_discount}</td>
			</tr>
			<!-- {payment_discount end if}-->
			<tr>
				<td>
					<div class="singleline">{total_lbl}:</div>
				</td>
				<td width="100" style="text-align: right;">
					<div class="singleline">{total}</div>
				</td>
			</tr>
			</tbody>
		</table>
		{checkout_button}
	</div>
	<!-- endtotal cart -->
</div>
</div>