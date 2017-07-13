<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/Common/FactoryMethod.php');
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Product.php');
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Paper.php');
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Option.php');
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/BasicMaterials/Afterprocess.php');

$products = $_SERVER["DOCUMENT_ROOT"] .'/test/Products/';
foreach (glob($products . '*.php') as $filename) {
	include $filename;
}

$after = $_SERVER["DOCUMENT_ROOT"] .'/test/Afterprocesses/';
foreach (glob($after . '*.php') as $filename) {
	include $filename;
}

$after = $_SERVER["DOCUMENT_ROOT"] .'/test/Papers/';
foreach (glob($after . '*.php') as $filename) {
	include $filename;
}

class DPrintingFactory extends FactoryMethod
{
	/**
	 * {@inheritdoc}
	 */
	function createPrintout($type)
	{
		switch ($type) {
			case '001001001':
				return new Namecard($type);
				break;
			case '001001002':
				return new Namecard($type);
				break;
			case '001001004':
				return new Namecard($type);
				break;
			case '001002016':
				return new Namecard($type);
				break;
			case '001003024':
				return new Namecard($type);
				break;
			case '002003001':
				return new Sticker($type);
				break;
			case '002003009':
				return new Sticker($type);
				break;
			case '002004009':
				return new Sticker_Thomson($type);
				break;
			case '003001001':
				return new Leaflet($type);
				break;
			case '003003001':
				return new Leaflet($type);
				break;
			case '003004001':
				return new Leaflet($type);
				break;
			case '005001001':
				return new Envelope_Master($type);
				break;
			case '005003001':
				return new Envelope_Master($type);
				break;
			case '005003002':
				return new Envelope_Master($type);
				break;
			case '005003003':
				return new Envelope_Master($type);
				break;
			case '005003004':
				return new Envelope_Master($type);
				break;
			case '005004001':
				return new Envelope_Master($type);
				break;
			case '005004002':
				return new Envelope_Master($type);
				break;
			case '005004003':
				return new Envelope_Master($type);
				break;
			case '005004004':
				return new Envelope_Master($type);
				break;
			case '005004005':
				return new Envelope_Master($type);
				break;
			case '005004006':
				return new Envelope_Master($type);
				break;
			case '005004007':
				return new Envelope_Master($type);
				break;
			case '005004008':
				return new Envelope_Master($type);
				break;
			case '005005001':
				return new Envelope_Master($type);
				break;
			case '005005002':
				return new Envelope_Master($type);
				break;
			case '005005003':
				return new Envelope_Master($type);
				break;
			case '005005004':
				return new Envelope_Master($type);
				break;
			case '005006001':
				return new Envelope_Master($type);
				break;
			case '005006002':
				return new Envelope_Master($type);
				break;
			case '005007001':
				return new Envelope_Master($type);
				break;
			case '005008001':
				return new Envelope_Master($type);
				break;
			case '005009001':
				return new Envelope_Master($type);
				break;
			case '005010001':
				return new Envelope_Master($type);
				break;
			case '005011001':
				return new Envelope_Master($type);
				break;
			case '005012001':
				return new Envelope_Master($type);
				break;
			case '005001002':
				return new Page($type);
				break;
			case '007001001':
				return new Master_NCR($type);
				break;
			case '007001002':
				return new Master_NCR($type);
				break;
			case '007001003':
				return new Master_NCR($type);
				break;
			case '007002001':
				return new Master_Form($type);
				break;
			default:
				return new CommonProduct($type);
				break;
		}
	}

	function createAfter($product, $type)
	{
		switch ($type) {
			case '재단':
				return new Cutting($product);
				break;
			case '미싱':
				return new Dotline($product);
				break;
			case '박':
				return new Foil($product);
				break;
			case '오시':
				return new Impression($product);
				break;
			case '형압':
				return new Press($product);
				break;
			case '타공':
				return new Punching($product);
				break;
			case '귀도리':
				return new Rounding($product);
				break;
			case '엠보싱':
				return new Embossing($product);
				break;
			case '코팅':
				return new Coating($product);
				break;
			case '제본':
				return new Binding($product);
				break;
			case '빼다':
				return new Background($product);
				break;
			case '접착':
				return new Bonding($product);
				break;
			case '넘버링':
				return new Numbering($product);
				break;
			default:
				return NULL;
				break;
		}
	}

	function createPaper($product, $type)
	{
		switch ($type) {
			case '007001001':
				return new Paper_NCR($product);
				break;
			case '007001002':
				return new Paper_NCR($product);
				break;
			case '007001003':
				return new Paper_NCR($product);
				break;
			case '002004009':
				return new Paper_ThomsonSticker($product);
				break;
			default:
				return new Paper($product);
				break;
		}
	}
}
?>