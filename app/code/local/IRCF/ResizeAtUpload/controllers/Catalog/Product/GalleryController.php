<?php

require_once('Mage/Adminhtml/controllers/Catalog/Product/GalleryController.php');

class IRCF_ResizeAtUpload_Catalog_Product_GalleryController extends Mage_Adminhtml_Catalog_Product_GalleryController{
	
	public function isResizeEnabled(){
		return Mage::getStoreConfig('resizeatupload/settings/enable') && $this->getResizeMaxWidth()+$this->getResizeMaxWidth()>0;
	}
	
	public function getResizeMaxWidth(){
		return Mage::getStoreConfig('resizeatupload/settings/maxwidth');
	}
	
	public function getResizeMaxHeight(){
		return Mage::getStoreConfig('resizeatupload/settings/maxheight');
	}
	
	public function uploadAction(){
		if($this->isResizeEnabled()){
			$imageTmpName = $_FILES['image']['tmp_name'];
			$imageInfo = getimagesize($imageTmpName);
			list ($type, $subtype) = explode('/', $imageInfo['mime']);
			if ($type == 'image'){
				$image = new Varien_Image($imageTmpName);
				$image->constrainOnly(false);
				$image->keepFrame(false);
				$image->keepAspectRatio(true);
				$image->keepTransparency(true);
				if (
					$this->getResizeMaxHeight() > 0
					&& (
						$this->getResizeMaxWidth() <= 0
						|| $imageInfo['0']/$imageInfo['1'] < $this->getResizeMaxWidth()/$this->getResizeMaxHeight()
					)
				){
					if($imageInfo['1'] > $this->getResizeMaxHeight()){
						$image->resize(null, $this->getResizeMaxHeight());
					}
				}else{
					if($imageInfo['0'] > $this->getResizeMaxWidth()){
						$image->resize($this->getResizeMaxWidth(), null);
					}
				}
				$image->save($imageTmpName);
			}
		}
		parent::uploadAction();
	}
}
