<?php

class FactoryMethod
{
	function createPrintout($type) {}

	function create($type)
	{
		$obj = $this->createPrintout($type);

		return $obj;
	}

	function createAfter($product ,$type)
	{
		$obj = $this->createAfter($product ,$type);

		return $obj;
	}

	function createOpt($type)
	{
		$obj = $this->createOpt($type);

		return $obj;
	}
}

?>