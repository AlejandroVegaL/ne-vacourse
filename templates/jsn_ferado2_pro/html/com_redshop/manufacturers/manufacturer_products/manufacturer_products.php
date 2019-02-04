<h1 class="manufacturer_name">{manufacturer_name}</h1>
<div class="manufacturer_image">{manufacturer_image}</div>
<div class="manufacturer_description">{manufacturer_description}</div>
<div id="category_header">
	<div class="category_order_by">{order_by}</div>
</div>
<div class="category_box_wrapper row grid">
	{product_loop_start}
	<div class="category_box_outside col-sm-4">
		<div class="category_box_inside">
			<div class="category_product_image">
				{product_thumb_image}
				<div class="category_product_content">
					<div class="category_product_title">
						<h3>{product_name}</h3>
						<div class="product_desc">{product_s_desc}</div>
					</div>
				</div>
			</div>
			<div class="category_product_info">	
				<div class="category_product_addtocart">{form_addtocart:add_to_cart1}</div>
				<div class="category_product_price">
					{if product_on_sale}
					<div class="category_product_oldprice">
						{product_old_price}
					</div>
					{product_on_sale end if}

					{product_price}

				</div>				
			</div>
		</div>
	</div>
	{product_loop_end}
</div>
<div class="pagination">{pagination}</div>
