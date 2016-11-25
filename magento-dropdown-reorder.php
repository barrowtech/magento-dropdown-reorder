<?php

class reorder	{

	public function __construct() {
			// MAGENTO
			require_once '../app/Mage.php';
			umask(0);
			Mage::app();
			Mage::register('isSecureArea', 1);
		}
	
	
	public function reorderconfigurable()	{
			
		echo "count collection of configurables = " . count($_collection) . " @" . __LINE__ . PHP_EOL;
		$file = 'sku_type_size_finish.csv';

		//Open the file.
		$fileHandle = fopen($file, "r");
 
		//Loop through the CSV rows.
		while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
			
			$_collection = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('type_id', array('eq' => 'configurable'))
                        ->addAttributeToFilter('SKU', $row[0])
        	        ;
	

			foreach($_collection as $_product){
				echo "---------------------------- @" . __LINE__ . PHP_EOL;
				echo "product = " .  $_product->getSKU() . PHP_EOL;
			
				$productTypeInstance = $_product->getTypeInstance();
				$attributes_array = $productTypeInstance->getConfigurableAttributesAsArray();
				
				$attCount = count($attributes_array);
				
				$sku = $_product->getSKU();
				echo $sku . PHP_EOL;
	
				if(($attCount > 1)){
					echo "product = " .  $_product->getName() . PHP_EOL;
				
					foreach($attributes_array as &$att)	{
						if($att['attribute_code']=='size')	{
							$att['position'] = 2;
						}
						if($att['attribute_code']=='finish')	{
							$att['position'] = 3;
						}
						if($att['attribute_code']=='typefilter')	{
							$att['position'] = 1;
						}
						//if($att['attribute_code']=='number')	{
						//	$att['position'] = 2;
						//}
					}
	
					$_product->setConfigurableAttributesData($attributes_array);
					$_product->save();
				}
			}
		}
	}

}

$order = new reorder();
$order->reorderconfigurable();