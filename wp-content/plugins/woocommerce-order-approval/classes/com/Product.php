<?php 
namespace WCOA\classes\com;

class Product
{
	public function __construct()
	{
		add_action('wp_ajax_wcoa_get_product_list', array(&$this, 'ajax_load_product_list'));
		add_action('wp_ajax_wcoa_get_category_list', array(&$this, 'ajax_load_category_list'));
		add_action('wp_ajax_wcoa_get_tag_list', array(&$this, 'ajax_load_tag_list'));
	}
	public function ajax_load_category_list()
	{
		if(isset($_GET['product_category']))
		{
			$product_categories = $this->get_product_taxonomy_list($_GET['product_category']);
			echo json_encode( $product_categories);
		}
		else 
			echo json_encode("");
		wp_die();
	}
	public function ajax_load_tag_list()
	{
		if(isset($_GET['product_tag']))
		{
			$product_tags = $this->get_product_taxonomy_list($_GET['product_tag'], 'product_tag');
			echo json_encode( $product_tags);
		}
		else 
			echo json_encode("");
		wp_die();
	}
	function ajax_load_product_list()
	{
		
		$resultCount = 50;
		$search_string = isset($_GET['search_string']) ? $_GET['search_string'] : null;
		$page = isset($_GET['page']) ? $_GET['page'] : null;
		$offset = isset($page) ? ($page - 1) * $resultCount : null;
		$product_list = $this->get_product_list($search_string ,$offset, $resultCount);
		echo json_encode( $product_list); 
		
		wp_die();
	}
	function get_product_list($search_string, $offset, $resultCount)
	{
		global $wpdb, $wcoa_wpml_model;
		 $query_select_string = "SELECT products.ID as id, products.post_parent as product_parent, products.post_title as product_name, product_meta.meta_value as product_sku";
		 $query_select_count_string = "SELECT COUNT(*) as tot";
		 $query_from_string = " FROM {$wpdb->posts} AS products
								 LEFT JOIN {$wpdb->postmeta} AS product_meta ON product_meta.post_id = products.ID AND product_meta.meta_key = '_sku'
								 WHERE  (products.post_type = 'product' OR products.post_type = 'product_variation')
								 AND products.post_status = 'publish' 
								";
		if($search_string)
				$query_from_string .=  " AND ( products.post_title LIKE '%{$search_string}%' OR product_meta.meta_value LIKE '%{$search_string}%' OR products.ID LIKE '%{$search_string}%' ) 
										AND (products.post_type = 'product' OR products.post_type = 'product_variation') ";
		
		$final_query_string =  $query_select_string.$query_from_string." GROUP BY products.ID LIMIT {$offset}, {$resultCount}";
		
		$result = $wpdb->get_results($final_query_string ) ;
		
		if($wcoa_wpml_model->wpml_is_active())
		{
			$product_ids = $variation_ids = array();
			foreach($result as $product)
			{
				if($product->product_parent == 0 )
					$product_ids[] = $product;
				else
					$variation_ids[] = $product;
			}
			
			//Filter products
			if(!empty($product_ids))
				$product_ids = $wcoa_wpml_model->remove_translated_id($product_ids, 'product', true);
			
			//Filter variations
			if(!empty($variation_ids))
				$variation_ids = $wcoa_wpml_model->remove_translated_id($variation_ids, 'product', true);
			
			$result = array_merge($product_ids, $variation_ids);
		}
		
		if(isset($result) && !empty($result))
			foreach($result as $index => $product)
				{
					if($product->product_parent != 0 )
					{
						$readable_name = $this->get_variation_complete_name($product->id);
						$result[$index]->product_name = $readable_name != false ? "<i>".__('Variation','woocommerce-files-upload')."</i> ".$readable_name : $result[$index]->product_name;
					}
				}
		
		
		if(isset($offset) && isset($resultCount))
		{
			$num_order = $wpdb->get_col($query_select_count_string.$query_from_string);
			$num_order = isset($num_order[0]) ? intval($num_order[0]) : 0;
			
			$endCount = $offset + $resultCount;
			$morePages = empty($result) ? false : $num_order > $endCount;
			$results = array(
				  "results" => $result,
				  "pagination" => array(
					  "more" => $morePages
				  )
			  );
		}
		else
			$results = array(
				  "results" => $result,
				  "pagination" => array(
					  "more" => false
				  )
			  );
		
		return $results;
	}
	public function get_product_taxonomy_list($search_string = null, $taxonomy_name = 'product_cat')
	 {
		 
		 global $wpdb, $wcoa_wpml_model;
		  $query_string = "SELECT product_categories.term_id as id, product_categories.name as category_name
							 FROM {$wpdb->terms} AS product_categories
							 LEFT JOIN {$wpdb->term_taxonomy} AS tax ON tax.term_id = product_categories.term_id 							 						 	 
							 WHERE tax.taxonomy = '{$taxonomy_name}' 
							 AND product_categories.slug <> 'uncategorized' 
							";
		 if($search_string)
					$query_string .=  " AND ( product_categories.name LIKE '%{$search_string}%' )";
			
		$query_string .=  " GROUP BY product_categories.term_id ";
		$result = $wpdb->get_results($query_string ) ;
		
		//WPML
		if($wcoa_wpml_model->wpml_is_active())
		{
			$result = $wcoa_wpml_model->remove_translated_id($result, $taxonomy_name, true);
		} 
		
		return $result;
	 }
	 public function get_variation_complete_name($variation_id)
	 {
		$error = false;
		$variation = wc_get_product($variation_id);
		
		if($variation == null || $variation == false)
			return "";
		if($variation->is_type('simple') || $variation->is_type('variable'))
			return $variation->get_title();
		
		
		$product_name = $variation->get_title()." - ";	
		if($product_name == " - ")
			return false;
		$attributes_counter = 0;
		foreach($variation->get_variation_attributes( ) as $attribute_name => $value)
		{
			
			if($attributes_counter > 0)
				$product_name .= ", ";
			$meta_key = urldecode( str_replace( 'attribute_', '', $attribute_name ) ); 
			
			$product_name .= " ".wc_attribute_label($meta_key).": ".$value;
			$attributes_counter++;
		}
		return $product_name;
	 }
	public function get_product_name($product_id, $include_id = true)
	{
		global $wcoa_wpml_model;
		$product_id = $wcoa_wpml_model->get_main_language_id($product_id, 'product');
		
		$product = wc_get_product($product_id);
		
		if(!isset($product) || $product === false)
			return "";
		
		if($product->get_type() == 'variation')
		{
			$readable_name = $this->get_variation_complete_name($product_id);
			$readable_name = $include_id ? "#".$product_id." - ".$readable_name  : $readable_name;
		}
		else
		{
			try{
			    $readable_name = $include_id ? $product->get_formatted_name() : $product->get_name();
		    }catch (Exception $e){}
		}
		return $readable_name; 
	}
	public function get_product_category_name($category_id, $default = false)
	{
		global $wcoa_wpml_model;
		$category_id = $wcoa_wpml_model->get_main_language_id($category_id, 'product_cat');
		$category = get_term( $category_id, 'product_cat' );
		return isset($category) ? $category->name : $default;
	}
	public function get_product_tag_name($tag_id, $default = false)
	{
		global $wcoa_wpml_model;
		$tag_id = $wcoa_wpml_model->get_main_language_id($tag_id, 'product_tag');
		$tag = get_term( $tag_id, 'product_tag' );
		return isset($tag) ? $tag->name : $default;
	}
}
?>