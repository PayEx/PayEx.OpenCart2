<?php 

class OcRoute
{
	public static function getPaymentRoute($route='')
	{
		if(version_compare(VERSION,'2.3.0.0','>=')){
			return 'extension/'.$route;
		}elseif(version_compare(VERSION,'2.2.0.0','<=')){
			return $route;
		}
	}
	public static function getExtension()
	{
		if(version_compare(VERSION,'2.3.0.0','>=')){
			return 'extension/extension';
		}elseif(version_compare(VERSION,'2.2.0.0','<=')){
			return 'extension/payment';
		}
	}
	public static function getTemplate($route)
	{
		if(version_compare(VERSION,'2.3.0.0','>=')){
			return ''.$route;
		}elseif(version_compare(VERSION,'2.2.0.0','>=')){
			return $route;
		} else {
		    // OC v2.0+
			return 'default/template/' . $route;
		}
	}
}