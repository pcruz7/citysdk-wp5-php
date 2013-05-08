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

require 'Request.php';
require 'UriTemplate.php';
require 'TourismExceptions.php';

/**
 * \brief CitySDK Tourism API client stub 
 *
 * @author Pedro Cruz
 */
class TourismClient {
	private $_hypermedia;
	private $_version;
	
	/**
 	 * \brief Default constructor
 	 *
 	 * @param $uri the home URI a CitySDK Tourism enabled endpoint
 	 */
	public function __construct($uri) {
		session_start();
		
		if(isset($_SESSION[$uri])) {
			$this->decodeVersions($_SESSION[$uri]);
		} else {
			$request = new Request($uri);
			$request->execute();
			if($request->responseInfo['http_code'] == '200') {
				$_SESSION[$uri] = json_decode($request->responseBody, true);
				$this->decodeVersions($_SESSION[$uri]);
			}
		}
		
		session_write_close();
		return $this;
	}
	
	/**
 	 * \brief Tells the stub to use a given version
 	 *
 	 * @param string $version the version to use
 	 */
	public function useVersion($version = '') {
		$this->_version = $version;
	}
	
	/**
     * \brief Gets the available resources for the visited server.
     * @retval array a mapping of available resources.
     * @throws VersionNotAvailableException thrown if the version was not set or is not available.
     */
	public function getResources() {
		$this->verifyVersion();
		return array_keys($this->_hypermedia[$this->_version]);
	}
	
	/**
     * \brief Checks whether the given resource is available or not.
     * @param string $resource the resource to check for availability.
     * @returns bool true if available, false otherwise.
     * @throws VersionNotAvailableException thrown if the version was not set or is not available.
     */
	public function hasResource($resource = '') {
		verifyVersion();
		return isset($this->_hypermedia[$this->_version][$resource]);
	}
	
	/**
     * \brief Checks whether the given parameter is available in a resource or not.
     * @param string $resource the resource to check the parameter.
     * @param string $parameter the parameter to check for availability
     * @retval bool true if available, false otherwise.
     * @throws VersionNotAvailableException thrown if the version was not set or is not available.
     */
	public function hasResourceParameter($resource = '', $parameter = '') {
		verifyVersion();
		$uriTemplate = new UriTemplate($this->_hypermedia[$this->_version][$resource]['href']);
		return $uriTemplate->hasParameter($parameter);
	}
	
	/**
 	 * \brief Gets a list of Points of Interest that follow a given set of parameters.
 	 *
 	 * @param array $parameters the parameters to follow.
 	 * @retval JSON containing the list of Points of Interest.
 	 * @throws VersionNotAvailableException thrown if a given version is not available or was not set.
 	 * @throws ResourceNotAvailableException thrown if the Points of Interest listing is not available.
 	 * @throws InvalidParameterException thrown if a given parameter is invalid for Points of Interest listing.
 	 * @throws ServerErrorException thrown if the server returned an HTTP error.
 	 */
	public function getPois($parameters = array()) {
		return $this->getList($parameters, 'find-poi');
	}
	
	/**
 	 * \brief Gets a list of Events that follow a given set of parameters.
 	 *
 	 * @param array $parameters the parameters to follow.
 	 * @retval JSON containing the list of Events.
 	 * @throws VersionNotAvailableException thrown if a given version is not available or was not set.
 	 * @throws ResourceNotAvailableException thrown if the Events listing is not available.
 	 * @throws InvalidParameterException thrown if a given parameter is invalid for Events listing.
 	 * @throws ServerErrorException thrown if the server returned an HTTP error.
 	 */
	public function getEvents($parameters = array()) {
		return $this->getList($parameters, 'find-event');
	}
	
	/**
 	 * \brief Gets a list of Routes that follow a given set of parameters.
 	 *
 	 * @param array $parameters the parameters to follow.
 	 * @retval JSON containing the list of Routes.
 	 * @throws VersionNotAvailableException thrown if a given version is not available or was not set.
 	 * @throws ResourceNotAvailableException thrown if the Routes listing is not available.
 	 * @throws InvalidParameterException thrown if a given parameter is invalid for Routes listing.
 	 * @throws ServerErrorException thrown if the server returned an HTTP error.
 	 */
	public function getRoutes($parameters = array()) {
		return $this->getList($parameters, 'find-route');
	}
	
	/**
 	 * \brief Gets a list of Categories of either Points of Interest, Events or Routes.
 	 *
 	 * @param array $parameters the parameters that should be followed. It should contain a list term with a value either poi, event or route.
 	 * @retval JSON containing the list of Categories.
 	 * @throws VersionNotAvailableException thrown if a given version is not available or was not set.
 	 * @throws ResourceNotAvailableException thrown if the Categories listing is not available.
 	 * @throws InvalidParameterException thrown if a given parameter is invalid for Categories listing.
 	 * @throws InvalidTermException thrown if the list term is not either pois, events or routes.
 	 * @throws ServerErrorException thrown if the server returned an HTTP error.
 	 */
	public function getCategories($parameters = array()) {
		return $this->getCategorization($parameters, 'find-categories');
	}
	
	/**
 	 * \brief Gets a list of Tags of either Points of Interest, Events or Routes.
 	 *
	 * @param array $parameters the parameters that should be followed. It should contain a list term with a value either poi, event or route.
 	 * @retval JSON containing the list of Tags.
 	 * @throws VersionNotAvailableException thrown if a given version is not available or was not set.
 	 * @throws ResourceNotAvailableException thrown if the Tags listing is not available.
	 * @throws InvalidParameterException thrown if a given parameter is invalid for Tags listing.
 	 * @throws InvalidTermException thrown if the list term is not either pois, events or routes.
 	 * @throws ServerErrorException thrown if the server returned an HTTP error.
 	 */
	public function getTags($parameters = array()) {
		return $this->getCategorization($parameters, 'find-tags');
	}
	
	/**
     * \brief Request for a single POI-based object (Point of Interest, Event or Route)
     * @param string $base the base URI of the POI.
     * @param string $id the id of the POI.
     * @retval JSON containing the POI.
     * @throws ServerErrorException thrown if the server returned an HTTP error.
	 */
	public function getPOI($base = '', $id = '') {
		return $this->makeSingleCall($base . $id);
	}
	
	/**
     * \brief Returns a list of Points of Interest with the given relation with the POI
	 * identified by base and id. The relation should be either: child or parent
     * @memberOf TourismClient
     * @param string $base the base URI of the related POI.
     * @param string $id the id of the related POI.
	 * @param string $relation the relationship to search for
	 * @throws InvalidTermException thrown if the relation is an invalid term.
	 * @throws ResourceNotAvailable thrown if getting a POI relations is unavailable for the server. 
     * @throws VersionNotAvailableException thrown if the version was not set or is not available.
     * @throws ServerErrorException thrown if the server returned an HTTP error.
     */
	private function getPoiRelation($base = '', $id = '', $relation = '') {
		return $this->getRelation($base, $id, $relation, 'find-poi-relation');
	}
	
	/**
     * \brief Returns a list of Event with the given relation with the POI
	 * identified by base and id. The relation should be either: child or parent
     * @memberOf TourismClient
     * @param string $base the base URI of the related Event.
     * @param string $id the id of the related Event.
	 * @param string $relation the relationship to search for
	 * @throws InvalidTermException thrown if the relation is an invalid term.
	 * @throws ResourceNotAvailable thrown if getting an Event relations is unavailable for the server. 
     * @throws VersionNotAvailableException thrown if the version was not set or is not available.
     * @throws ServerErrorException thrown if the server returned an HTTP error.
     */
	private function getEventRelation($base = '', $id = '', $relation = '') {
		return $this->getRelation($base, $id, $relation, 'find-event-relation');
	}
	
	private function getList($parameters, $resource) {
		$this->verifyVersion();
		$this->validateResource($resource);
		$this->validateParameters($parameters, $this->_hypermedia[$this->_version][$resource]);
		return $this->makeQueryCall($this->_hypermedia[$this->_version][$resource], $parameters);
	}
	
	private function getCategorization($parameters, $resource) {
		$this->verifyVersion();
		$this->validateResource($resource);
		$this->validateParameters($parameters, $this->_hypermedia[$this->_version][$resource]);
		if(isset($parameters['list']))
			$this->validateTerm($parameters['list']);
		else
			throw new InvalidTermException('list parameter must be set');
		
		return $this->makeQueryCall($this->_hypermedia[$this->_version][$resource], $parameters);
	}
	
	private function getRelation($base, $id, $relation, $resource) {
		$this->verifyVersion();
		$this->validateResource($resource);
		$this->validateRelation($relation);
		
		$parameters = array (
			'base' => $base,
			'id' => $id,
			'relation' => $relation
		);
		
		return $this->makeQueryCall($this->_hypermedia[$this->_version][$resource], $parameters);
	}
	
	private function makeQueryCall($resource, $parameters) {
		$uri = $resource['href'];
		if($resource['templated']) {
			$template = new UriTemplate($resource['href']);
			$keys = array_keys($parameters);
			foreach($keys as $key) {
				$template->set($key, $parameters[$key]);
 			}
 			
 			$uri = $template->build();
		}
		
		return $this->makeSingleCall($uri);
	}
	
	private function makeSingleCall($uri) {
		$request = new Request($uri);
		$request-> execute();
		
		if($request->responseInfo['http_code'] == 200)
			return json_decode($request->responseBody, true);
		else
			throw new ServerErrorException('Server returned ' . $request->responseInfo['http_code']);
	}
	
	private function verifyVersion() {
		if(!isset($this->_version)) {
			throw new VersionNotAvailableException('Version must be set');
		}
		
		if(!isset($this->_hypermedia[$this->_version])) {
			throw new VersionNotAvailableException($this->_version . ' is not available in this server');
		}
	}
	
	private function validateResource($resource) {
		if(!isset($this->_hypermedia[$this->_version][$resource])) {
			throw new ResourceNotAvailableException($resource . ' is not available in this server');
		}
	}
	
	private function validateParameters($parameters, $resource) {
		if(!isset($parameters))
			throw new InvalidParameterException('Parameter cannot be null');
			
		if($resource['templated']) {
			$uriTemplate = new UriTemplate($resource['href']);
			$keys = array_keys($parameters);
			foreach($keys as $key) {
				if(!$uriTemplate->hasParameter($key)) {
					throw new InvalidParameterException($key . ' is not a valid parameter');
				}
 			}
		}
 	}
 	
 	private function validateTerm($term) {
 		if(!isset($term) 
 			|| ($term != 'poi'
 				&& $term != 'event'
 				&& $term != 'route'))
 			throw new InvalidTermException($term . ' is an invalid term');
 	}
 	
 	private function validateRelation($relation) {
 		if(!isset($relation) 
 			|| ($relation != 'parent'
 				&& $relation != 'child'))
 			throw new InvalidTermException($term . ' is an invalid term');
 	}
		
	private function decodeVersions($hypermedia) {
		$versions = $hypermedia['citysdk-tourism'];
		foreach($versions as $version) {
			$this->_hypermedia[$version['version']] = $version['_links'];
		}
	}
}

?>