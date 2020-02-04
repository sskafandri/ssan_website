<?php
class ControllerExtensionFeedGoogleSitemap extends Controller {
	public function index() {
		if ($this->config->get('google_sitemap_status')) {
			$output  = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

			$this->load->model('catalog/product');
			$this->load->model('tool/image');

	/*		$products = $this->model_catalog_product->getProducts();

			foreach ($products as $product) {
				if ($product['image']) {
					$output .= '<url>';
					$output .= '<loc>' . $this->url->link('product/product', 'product_id=' . $product['product_id']) . '</loc>';
					$output .= '<changefreq>daily</changefreq>';
					$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) . '</lastmod>';
					$output .= '<priority>1.0</priority>';
					$output .= '<image:image>';
					$output .= '<image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
					$output .= '<image:caption>' . $product['name'] . '</image:caption>';
					$output .= '<image:title>' . $product['name'] . '</image:title>';
					$output .= '</image:image>';
					$output .= '</url>';
				}
			}
*/
			$this->load->model('catalog/category');

			$output .= $this->getCategories(0);
		//	$output = $this->getCategories(0);

		//	$this->load->model('catalog/manufacturer');

		/*	$manufacturers = $this->model_catalog_manufacturer->getManufacturers();

			foreach ($manufacturers as $manufacturer) {
				$output .= '<url>';
				$output .= '<loc>' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id']) . '</loc>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>0.7</priority>';
				$output .= '</url>';

				$products = $this->model_catalog_product->getProducts(array('filter_manufacturer_id' => $manufacturer['manufacturer_id']));

				foreach ($products as $product) {
					$output .= '<url>';
					$output .= '<loc>' . $this->url->link('product/product', 'manufacturer_id=' . $manufacturer['manufacturer_id'] . '&product_id=' . $product['product_id']) . '</loc>';
					$output .= '<changefreq>weekly</changefreq>';
					$output .= '<priority>1.0</priority>';
					$output .= '</url>';
				}
			}
*/
			$this->load->model('catalog/information');

			$informations = $this->model_catalog_information->getInformations();

			foreach ($informations as $information) {
				$output .= '<url>';
				$output .= '<loc>' . $this->url->link('information/information', 'information_id=' . $information['information_id']) . '</loc>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>0.5</priority>';
				$output .= '</url>';
			}

			$output .= '</urlset>';

			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}
	}

	protected function getCategories($parent_id, $current_path = '') {
		$output = '';

		$results = $this->model_catalog_category->getCategories($parent_id);
		$count = 0;
		foreach ($results as $result) {
			$count++;
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}

			$output .= '<url>';
			$tmp =  $this->url->link('product/category', 'path=' . $new_path);
			$output .= '<loc>' . $this->url->link('product/category', 'path=' . $new_path) . '</loc>';
			$output .= '<changefreq>daily</changefreq>';
			$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime(date('Y-m-d',strtotime("-1 days")))) . '</lastmod>';
			$output .= '<priority>0.8</priority>';
			$output .= '</url>';

			$products = $this->model_catalog_product->getProducts(array('filter_category_id' => $result['category_id']));

			foreach ($products as $product) {
				$count++;
			//	$output = "";

				if(($product['product_id'] == 5168) && ($result['category_id'] == 567) ){
				//	$output = "";
					$tmp = str_replace('&', 'and', $product['name']);

				}
				$cleanname = str_replace('&', 'and', $product['name']);
				$cleanname = str_replace('-', '', $cleanname);
				$tmpval = $result['category_id'].'_'.$product['product_id'];
				$output .= '<url>';
				//$output .= '<productid>'.$tmpval.'</productid>';

				$output .= '<loc>' . $this->url->link('product/product', 'path=' . $new_path . '&product_id=' . $product['product_id']) . '</loc>';
				$output .= '<changefreq>daily</changefreq>';
				$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime(date('Y-m-d',strtotime("-1 days")))) . '</lastmod>';
				$output .= '<priority>1.0</priority>';

				$tmpimage = '<image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
				if(strripos($tmpimage, '&') === false)
				{
				$output .= '<image:image>';
				$output .= '<image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
				$output .= '<image:caption>' . $cleanname . '</image:caption>';
				$output .= '<image:title>' . $cleanname . '</image:title>';
				$output .= '</image:image>';
				}
				$output .= '</url>';
			}

			$output .= $this->getCategories($result['category_id'], $new_path);
		}

		return $output;
	}
}