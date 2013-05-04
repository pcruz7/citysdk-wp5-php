<?php
/*
 * COPYRIGHT NOTICE: 
 *
 * This file is part of CitySDK WP5 Tourism PHP Library.
 *
 * CitySDK WP5 Tourism PHP Library is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * CitySDK WP5 Tourism PHP Library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with CitySDK WP5 Tourism PHP Library. If not, see <http://www.gnu.org/licenses/>.
 * 
 * Copyright 2013, IST
 */

require 'Operators.php';

/**
 * \brief Class following the Uri Template RFC (<a target="_blank" href="http://tools.ietf.org/html/rfc6570">RFC6570</a>)
 *
 * @author Pedro Cruz
 */
class UriTemplate {
	private $_template;
	private $_values;
	private $templateRegex = "/{[^{}]+}/";
	private $keysRegex = "/(?:\w+)/";
	
	/**
     * \brief Default constructor
     *
     * @param string $uri the templated URI to build
     */
	public function __construct($uri) {
		$this->_template = $uri;
		$this->_values = array();
	}
	
	/**
     * \brief Sets a given parameter with its value
     *
     * @param string $parameter the name of the parameter
     * @param $value the value of the parameter (should be either a primitive type or an array)
     * @throw InvalidValueTypeException thrown if the value is not primitive or an array
     */
	public function set($parameter, $value) {
	    if(!$this->isSimpleType($value)
	        && !$this->isArray($value))
	        throw new InvalidValueTypeException($parameter . ' should have a primitive type or an array as its value');
	        
		if(!in_array($parameter, $this->_values)) {
			$this->_values[$parameter] = $value;
		}
	}
	
	/**
     * \brief Checks whether a parameter exists in the templated URI
     *
     * @param string $param the parameter to check
     * @retval bool true if it exists, false otherwise
     */
	public function hasParameter($param) {
		preg_match_all($this->templateRegex, $this->_template, $keywords);
		foreach($keywords[0] as $keyword) {
			preg_match_all($this->keysRegex, $keyword, $parameters);
			foreach($parameters[0] as $parameter) {
				if($parameter == $param)
					return true;
			}
		}
		
		return false;
	}
	
	/**
     * \brief Builds the URI with the previously set values.
     *
     * @retval string the converted URI with the given set of values.
     */
	public function build() {
		$uri = $this->_template;
		$parameters = '';
		$size = -1;
		$getMod = true;
		
		preg_match_all($this->templateRegex, $this->_template, $keywords, PREG_OFFSET_CAPTURE);
		foreach($keywords[0] as $keyword) {
			$op = Operators::getOperator(substr($keyword[0], 1, 1));
			$parameters .= $op['value'];
			preg_match_all($this->keysRegex, $keyword[0], $vars, PREG_OFFSET_CAPTURE);
			foreach($vars[0] as $var) {
				$offset = $var[1] + strlen($var[0]);
				if($getMod)
					$modifier = $keyword[0][$offset];
				else
					$size = intval($var[0]);
				
				if($modifier == '*') {
					$parameters .= $this->explode($var[0], $op);
				} else if($modifier == ':') {
					if($size < 0) {
						$getMod = false;
						$previous = $var[0];
						continue;
					} else {
						$parameters .= $this->modify($previous, $size, $op);
						$size = -1;
						$getMod = true;
					}
				} else {
					$parameters .= $this->expand($var[0], $op);
				}
			}
			
			if($op['separator'] && $this->endsWith($parameters, $op['separator']))
				$parameters = substr($parameters, 0, strlen($parameters) - 1);

		    $uri = str_replace($keyword[0], $parameters, $uri);
		}
		
		return str_replace(' ' , '%20', $uri);
	}
	
	private function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
	
	private function expand($vars, $op) {
		$parameters = '';
		if(isset($this->_values[$vars])) {
			if($this->isSimpleType($this->_values[$vars])) {
				$parameters .= $this->expandSimple($vars, $op, $this->_values[$vars]);
			} else if($this->isArray($this->_values[$vars])) {
				$parameters .= $this->expandArray($vars, $op, $this->_values[$vars]);
			}
		}
		
		return $parameters;
	}
	
	private function explode($vars, $op) {
	   $parameters = '';
		if(isset($this->_values[$vars])) {
			if($this->isSimpleType($this->_values[$vars])) {
				$parameters .= $this->expandSimple($vars, $op, $this->_values[$vars]);
			} else if($this->isArray($this->_values[$vars])) {
				$parameters .= $this->explodeArray($vars, $op, $this->_values[$vars]);
			}
		}
		
		return $parameters;
	}
	
	private function modify($vars, $size, $op) {
		$parameters = '';
		if(isset($this->_values[$vars])) {
			$value = substr($this->_values[$vars], 0, $size);
			if ($op['named'])
				$parameters .= $vars . "=" . $value;
			else
				$parameters .= $value;

			$parameters .= $op['separator'];
		}
        
		return $parameters;
	}
	
	private function expandSimple($vars, $op, $value) {
	    $parameters = '';
		if($op['named'])
			$parameters .= $vars . "=" . $value;
		else
			$parameters .= $value;
		
		$parameters .= $op['separator'];
		return $parameters;
	}
	
	private function expandArray($value, $op, $array) {
	    $isAssoc = $this->isAssoc($array);
	    $arr = array_keys($array);
	    $parameters = '';
	    $separator = ',';
		
		if($op['named'])
			$parameters .= $value . "=";
			
	    foreach($arr as $key) {
	        $ob = $array[$key];
			if(!$isAssoc) { // an indexed array
				$parameters .= $ob . $separator;
			} else { // an associative array
				$parameters .= $key . "," . $ob . $separator;
			}
	    }
	    
	    $parameters = substr($parameters, 0, strlen($parameters) - 1);
		$parameters .= $op['separator'];
		return $parameters;
	}
	
	private function explodeArray($vars, $op, $array) {
	    $isAssoc = $this->isAssoc($array);
	    $arr = array_keys($array);
	    $parameters = '';
		$separator = $op['separator']; 
		
	    foreach($arr as $key) {
	        $ob = $array[$key];
			if(!$isAssoc) { // an indexed array
				if ($op['named']) {
					$parameters .= $vars . "=" . $ob . $separator;
				} else {
					$parameters .= $ob . $separator;
				}
			} else { // associative array
				if ($op['named']) {
					$parameters .= $key . "=" . $ob . $separator;
				} else {
					$parameters .= $key . $op['separator'] . $ob . $separator;
				}
			}
	    }
	    
	    $parameters = substr($parameters, 0, strlen($parameters) - 1);
		$parameters .= $op['separator'];
		return $parameters;
	}
	
	private function isAssoc($array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }
	
	private function isSimpleType($vars) {
		return is_scalar($vars);
	}
	
	private function isArray($vars) {
		return is_array($vars);
	}
}
?>