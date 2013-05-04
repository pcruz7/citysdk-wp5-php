<?php
/*
 * COPYRIGHT NOTICE:
 *
 * This file is part of CitySDK WP5 Tourism Java Library.
 *
 * CitySDK WP5 Tourism Java Library is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * CitySDK WP5 Tourism Java Library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with CitySDK WP5 Tourism Java Library. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2013, IST
 */

/**
 * \brief Helper class for {@link UriTemplate}
 *
 * @author Pedro Cruz
 */
class Operators {
    
    /**
     * \brief Gets the operator corresponding to $op
     * @param string $op the desired operator
     * @retval the wanted operator or the default one. 
     */
    public static function getOperator($op = "") {
        static $_operators;
        if(!isset($_operators)) {
            $_operators = array (
                                 "" => array (
                                              "value" => "",
                                              "operator" => "",
                                              "separator" => ",",
                                              "named" => false
                                              ),
                                 "+" => array (
                                               "value" => "+",
                                               "operator" => "",
                                               "separator" => ",",
                                               "named" => false,
                                               ),
                                 "#" => array (
                                               "value" => "#",
                                               "operator" => "#",
                                               "separator" => ",",
                                               "named" => false,
                                               ),
                                 "." => array (
                                               "value" => ".",
                                               "operator" => ".",
                                               "separator" => ".",
                                               "named" => false,
                                               ),
                                 "/" => array (
                                               "value" => "/",
                                               "operator" => "/",
                                               "separator" => "/",
                                               "named" => false,
                                               ),
                                 ";" => array (
                                               "value" => ";",
                                               "operator" => ";",
                                               "separator" => ";",
                                               "named" => true,
                                               ),
                                 "?" => array (
                                               "value"  => "?",
                                               "operator" => "?",
                                               "separator" => "&",
                                               "named" => true,
                                               ),
                                 "&" => array (
                                               "value" => "&",
                                               "operator" => "&",
                                               "separator" => "&",
                                               "named" => true,
                                               )
                                 );
        }
        
        if(isset($_operators[$op]))
       		return $_operators[$op];
       	else
       		return $_operators[''];
    }
}
?>