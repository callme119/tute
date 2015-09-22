<?php

//按不同的关键字进行排序
function order_by($order)
{
	$by = I('get.by');
	$getOrder = I('get.order');

	//如果点击排序字段与上次点击相同，则改变排序顺序
	//如果不同，则只更改排序字段
	if($getOrder == $order)
	{
		if($by != 'desc')
		{
			$by = 'desc';
		}	
		else
		{
			$by = 'asc';
		}
	}	
	$return = U("?order=$order&by=$by" , I('get.'));
	return $return;
}

/**
 * 按数组中某一个KEY进行快速排序
 * @param  传入数组 $arrs    二维
 * @param  key $keyWord 关键KEY
 * @return 数组          排序后的数组
 */
function quick_sort($arrs , $keyWord = "" ,$orderBy = "asc") {

	//数组整体长度
	$len = count($arrs);

	if($keyWord == "")
		return $arrs;

	if ($len <= 1)
	{
		return $arrs;
	}

	$left = $right = array();

	//取第一个元素
	foreach($arrs as $key => $value)
	{
		$midValue = $value;
		$midKey = $key;
		$midValue[$keyWord] = isset($midValue[$keyWord]) ? $midValue[$keyWord] : 0;
		break;
	}

	//除第一个元素外，对其它原素进行比较
	foreach($arrs as $key => $value)
	{	
		if($key == $midKey)
			continue;
		$arrs[$key][$keyWord] = isset($arrs[$key][$keyWord]) ? $arrs[$key][$keyWord] : 0;
		
		//升序
		if($orderBy == 'asc')
		{
			if ($arrs[$key][$keyWord] < $midValue[$keyWord])
				$left[$key] = $arrs[$key];
			else
				$right[$key] = $arrs[$key];
		}

		//降序
		else
		{
			if ($arrs[$key][$keyWord] > $midValue[$keyWord])
				$left[$key] = $arrs[$key];
			else
				$right[$key] = $arrs[$key];
		}

	}

	//分别对左半部分及右半部分排序
	$left = quick_sort($left,$keyWord,$orderBy);
	$right = quick_sort($right,$keyWord,$orderBy);

	//进行数组的拼接，弃用自带array_merge方法。
	//原因：array_merge会将原有的键值覆盖掉。
	$return = array();
	foreach($left  as $key => $value)
	{
		$return[$key] = $value;
	}

	$return[$midKey] = $midValue;
	
	foreach($right  as $key => $value)
	{
		$return[$key] = $value;
	}

	return($return);
	// return array_merge(quick_sort($left,$keyWord,$i), (array)$arrs[$midKey], quick_sort($right,$keyWord,$i) );
}