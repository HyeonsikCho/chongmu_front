<?php
include_once($_SERVER["DOCUMENT_ROOT"] .'/test/Common/PrintoutInterface.php');
//include_once($_SERVER["DOCUMENT_ROOT"] .'/test/Products/Product.php');

class CommonProduct extends Product implements PrintoutInterface
{
	/**
	 * @var string
	 */
	var $amt;
	var $size_w, $size_h;
	var $papers;
	var $options;
	var $afterprocesses;

	public function __construct()
	{
		$this->papers = array();
		$this->options = array();
		$this->afterprocesses = array();
	}

	public function makeHtml() {
		return '<select name="select">
					<option value="value1">Value 1</option>
					<option value="value2" selected>Value 2</option>
					<option value="value3">Value 3</option>
				 </select>';
	}

	public function cost() {
		return 0;
	}
}

?>