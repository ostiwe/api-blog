<?php


namespace Blog\Helper;


class SetTypesHelper
{
	public static function handle(array $props): array
	{
		$_props = $props;
		foreach ($props as $propName => $propVal) {
			if (is_array($propVal)) {
				$_props[$propName] = self::handle($propVal);
			} else {
				if ((int)$propVal !== 0) $_props[$propName] = (int)$propVal;
			}
		}
		return $_props;
	}
}