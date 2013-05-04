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
 * \brief Thrown if a given version is not available
 *
 * @author Pedro Cruz
 */
class VersionNotAvailableException extends Exception { }
/**
 * \brief Thrown if a given resource is not available
 *
 * @author Pedro Cruz
 */
class ResourceNotAvailableException extends Exception { }
/**
 * \brief Thrown if a given parameter is invalid for a given resource
 *
 * @author Pedro Cruz
 */
class InvalidParameterException extends Exception { }
/**
 * \brief Thrown if a given parameter value has an invalid type
 *
 * @author Pedro Cruz
 */
class InvalidValueTypeException extends Exception { }
/**
 * \brief Thrown if a given term value is invalid
 *
 * @author Pedro Cruz
 */
class InvalidTermException extends Exception { }
/**
 * \brief Thrown if the server returned an HTTP code different from 200OK
 *
 * @author Pedro Cruz
 */
class ServerErrorException extends Exception { }
?>