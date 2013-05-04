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
 * \brief Abstraction to make REST calls
 *
 * @author Pedro Cruz
 */
class Request {
	/**
	 * \brief Contains the response body of this request
 	 */
	public $responseBody;
	/**
	 * \brief Contains the response info (e.g: HTTP code) of this request
 	 */
	public $responseInfo;
	
	private $_url;
	private $_requestType;
	private $_verb;
	private $_acceptType;
	
	/**
	 * \brief Default constructor
	 *
	 * @param string $url the url to make the request
 	 */
	public function __construct($url = null) {
		$this->_verb = 'GET';
		$this->_url = $url;
		$this->_requestType = 'application/json';	
		$this->_accepthType = $this->_requestType;
	}

	// sets the options for cURL
	protected function setCurlOpts (&$curlHandle) {
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);
		curl_setopt($curlHandle, CURLOPT_URL, $this->_url);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array ('Accept: ' . $this->_acceptType));
	}
	
	/**
	 * \brief Executes a given request to a URI.
 	 */
	public function execute () {
		$ch = curl_init();
		$this->setCurlOpts($ch);
		$this->responseBody = curl_exec($ch);
		$this->responseInfo	= curl_getinfo($ch);

		curl_close($ch);
	}
}

?>