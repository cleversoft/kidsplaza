<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Adminhtml_SolrController extends Mage_Adminhtml_Controller_Action {
	/**
	 * Test Solr Server Connection Action
	 */
	public function pingAction() {
		$host = $this->getRequest()->getParam('host');
		$port = $this->getRequest()->getParam('port');
		$path = $this->getRequest()->getParam('path');

		$data = array(
			'status' => '',
			'ping' => '',
			'solr' => '',
			'jvm' => '',
			'host' => ''
		);
		
		if (!class_exists('Apache_Solr_Service')){
			$data['status'] = 'Solr lib not found';
		}else{
			$client = new Apache_Solr_Service($host, $port, $path);
			$ping = $client->ping();

			if ($ping){
				$data['ping'] = $client->ping();
				$data['status'] = Mage::helper('mtsearch')->__('OK');
				$info = $client->system();
				$info = json_decode($info->getRawResponse());
				
				if ($info){
					foreach ($info->lucene as $k=>$v){
						if ($k == 'lucene-spec-version') $data['solr'] = $v;
					}
					foreach ($info->jvm as $k=>$v){
						if ($k == 'version') $data['jvm'] = $v;
					}
					foreach ($info->system as $k=>$v){
						if ($k == 'name') $data['host'] = $v;
					}
				}
			}else $data['status'] = Mage::helper('mtsearch')->__('Failed');
		}
		echo json_encode($data);
	}
}