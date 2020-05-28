<?php


namespace Blog\Helper;


class SetTypesHelper
{
	public static function handle(array $props)
	{
		$_props = $props;
		foreach ($props as $propName => $propVal) {
			if ((int)$propVal !== 0) $_props[$propName] = (int)$propVal;
		}
		return $_props;
	}
}