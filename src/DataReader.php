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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with CitySDK WP5 Tourism PHP Library. If not, see <http://www.gnu.org/licenses/>.
 * 
 * Copyright 2013, IST
 */

/**
 * \brief Used to parse single POI objects.
 *
 * @author Pedro Cruz
 */
class DataReader {
	/**
	 * \brief Some terms used by/for DataReader
	 */
	public static $term = array (
		'AUTHOR_TERM_PRIMARY' => 'primary',
		'AUTHOR_TERM_SECONDARY' => 'secondary',
		'AUTHOR_TERM_CONTRIBUTER' => 'contributer',
		'AUTHOR_TERM_EDITOR' => 'editor',
		'AUTHOR_TERM_PUBLISHER' => 'publisher',
	
		'LABEL_TERM_PRIMARY' => 'primary',
		'LABEL_TERM_NOTE' => 'note',
	
		'TIME_TERM_START' => 'start',
		'TIME_TERM_END' => 'end',
		'TIME_TERM_INSTANT' => 'instant',
		'TIME_TERM_OPEN' => 'open',
	
		'LINK_TERM_SOURCE' => 'source',			/*POI source information*/
		'LINK_TERM_ALTERNATE' => 'alternate',		/*a identical POI. Often used as a permalink*/
		'LINK_TERM_CANONICAL' => 'canonical',		/*the preferred version of a set of POIs with highly 
												  similar content. For example, there could be many 
												  different perceptions of a neighborhood boundary 
												  POI, but the city's neighborhood map could be the 
												  canonical version of this POI.*/
		'LINK_TERM_COPYRIGHT' => 'copyright',		/*a copyright statement that applys to the link's 
												  context*/
		'LINK_TERM_DESCRIBEDBY' => 'describedby', /*more information about this POI*/
		'LINK_TERM_EDIT' => 'edit',				/*a resource that can be used to edit the POI's 
												  context*/
		'LINK_TERM_ENCLOSURE' => 'enclosure',		/*a related resource that is potentially large 
												  and might require special handling*/
		'LINK_TERM_ICON' => 'icon',
		'LINK_TERM_LATEST_VERSION' => 'latest-version', /*points to a resource containing the latest 
														version*/
		'LINK_TERM_LICENSE' => 'license',				/*a license for this POI*/
		'LINK_TERM_RELATED' => 'related',				/*a related resource*/
		'LINK_TERM_SEARCH' => 'search',				/*a resource that can be used to search through 
													  the link's context and related resources*/
		'LINK_TERM_PARENT' => 'parent',				/*a parent POI, often the enclosing geographic 
													  entity, or the entity this POI in under the 
													  domain of  => such as a field office-corporate 
													  headquarters relationship*/
		'LINK_TERM_CHILD' => 'child',					/*a child POI, often a geography entity enclosed
													  or under the domain of this POI*/
		'LINK_TERM_HISTORIC' => 'historic',			/*links to a POI or other web resource that 
													  describes this place at a previous point in time*/
		'LINK_TERM_FUTURE' => 'future',				/*links to a POI or other web resource that 
													  describes this place at a later point in time*/
	
		'POINT_TERM_CENTER' => 'center',
		'POINT_TERM_NAVIGATION_POINT' => 'navigation point',
		'POINT_TERM_ENTRANCE' => 'entrance',
	
		'RELATIONSHIP_TERM_EQUALS' => 'equals',
		'RELATIONSHIP_TERM_DISJOINT' => 'disjoint',
		'RELATIONSHIP_TERM_CROSSES' => 'crosses',
		'RELATIONSHIP_TERM_OVERLAPS' => 'overlaps',
		'RELATIONSHIP_TERM_WITHIN' => 'within',
		'RELATIONSHIP_TERM_CONTAINS' => 'contains',
		'RELATIONSHIP_TERM_TOUCHES' => 'touches'
	);
	
	/**
 	 * \brief Gets the available languages for the POI object.
 	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
 	 * @param string $term which field should the languages be checked. Either label or description.
 	 * @retval array an associative array containing the available languages: code => locale or false
 	 */
	public static function getAvailableLanguages($poi, $term = 'label') {
		if(!isset($poi))
			return false;
		
		if($term != 'label'
			&& $term != 'description')
			return false;
			
		$labels = $poi[$term]; 
		$languages = array();

		foreach($labels as $label) {
			$lang = str_replace('-', '_', $label['lang']);
			$explode = explode('_', $lang);
			$languages[$explode[0]] = $label['lang'];
		}
		return $languages;
	}
	
	private static function isSameLang($oLang, $sLang) {
		$oLang = str_replace('-', '_', $oLang);
		$sLang = str_replace('-', '_', $sLang);
	
		$pL = explode('_', $oLang);
		$zL = explode('_', $sLang);
		return ($pL[0] == $zL[0]);
	}
	
	/**
 	 * \brief Gets a label description from the POI object with a given term in a given language.
 	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
 	 * @param string $term the wanted language. Defaults to primary.
 	 * @param string $lang the wanted language. Defaults to en_GB.
 	 * @retval string the label in the wanted language and term or false
 	 */
	public static function getLabel($poi, $term = 'primary', $lang = 'en_GB') {
		if(!isset($poi))
			return false;
		
		$labels = $poi['label'];
		if(isset($poiLang))
			$poiLang = $poi['lang'];
		else
			$poiLang = 'en_GB';
			
		$defaultValue = false;

		foreach($labels as $label) {
			if(!isset($label['lang'])) 
				$labelLang = $poiLang;
			else
				$labelLang = $label['lang'];
			
			if($label['term'] == $term 
				&& DataReader::isSameLang($lang, 'en_GB')) {
				$defaultValue = $label['value'];
			} else if($label['term'] == $term
					&& DataReader::isSameLang($lang, $labelLang)) {
				return $label['value'];
			} else if($label['term'] == $term) {
				$defaultValue = $label['value'];
			}
		}
		
		return $defaultValue;
	}
	
	/**
 	 * \brief Gets a given description from the POI object with a given language.
 	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
 	 * @param string $lang the wanted language. Defaults to en_GB.
 	 * @retval string the description in the wanted language or false
 	 */
	public static function getDescription($poi, $lang = 'en_GB') {
		if(!isset($poi) )
			return false;
		
		if(!isset($poi['description']))
			return false;
			
		$descriptions = $poi['description'];
		$poiLang = $poi['lang'];
		$defaultValue = false;

		foreach($descriptions as $description) {
			if(!isset($description['lang'])) 
				$descriptionLang = $poiLang;
			else
				$descriptionLang = $description['lang'];
			
			if(DataReader::isSameLang($lang, 'en_GB')) {
				$defaultValue = $description['value'];
			} else if(DataReader::isSameLang($lang, $descriptionLang)) {
				return $description['value'];
			}
		}
		
		return $defaultValue;
	}
	
	/**
	 * \brief Gets a price description from the POI object with a given language.
	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
	 * @param string $lang the wanted language. Defaults to en_GB.
	 * @retval string a price description in the given language, or in en_GB if
	 * the wanted language does not exist or false if both do not exist.
	 */
	public static function getPrice($poi, $lang = 'en_GB') {
		return DataReader::getValueWithType($poi, $lang, 'X-citysdk/price');
	}

	/**
	 * \brief Gets the waiting time description from the POI object with a given language.
	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
	 * @retval string the waiting time description (in seconds) or false.
	 */
	public static function getWaitingTime($poi) {
		return DataReader::getValueWithType($poi, null, 'X-citysdk/waiting-time');
	}

	/**
	 * \brief Gets the occupation description from the POI object with a given language.
	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
	 * @param string $lang the wanted language. Defaults to en_GB.
	 * @retval string the occupation description (0 to 100) or false.
	 */
	public static function getOccupation($poi, $lang = 'en_GB') {
		return DataReader::getValueWithType($poi, null, 'X-citysdk/occupation');
	}

	private static function getValueWithType($poi, $lang, $type) {
		if(!isset($poi))
			return false;
			
		$descriptions = $poi['description'];
		$poiLang = $poi['lang'];
		$defaultValue = '';

		foreach($descriptions as $description) {
			if(!isset($description['lang'])) 
				$descriptionLang = $poiLang;
			else
				$descriptionLang = $description['lang'];
			
			if(isset($description['type']) 
				&& $description['type'] == $type) {
				if(DataReader::isSameLang($lang, 'en_GB')) {
					$defaultValue = $description['value'];
				} else if(DataReader::isSameLang($lang, $descriptionLang)) {
					return $description['value'];
				}
			}
		}
		
		return $defaultValue;
	}
	
	/**
	 * \brief Gets all the thumbnail URI or base-64 bytecode from the POI object.
	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
	 * @retval array an array containing all the thumbnail URI or base-64 bytecode or an empty array.
	 */
	public static function getThumbnails($poi) {
		if(!isset($poi))
			return false;
			
		if(!isset($poi['link']))
			return false;
			
		$thumbnails = array();
		$links = $poi['link'];
		foreach($links as $link) {
			if(isset($link['term']) 
				&& $link['term'] == DataReader::$term['LINK_TERM_ICON']) {
				if(isset($link['href'])) {
					$content = new ImageContent($link['href']);
					array_push($thumbnails, $content);
				} else if(isset($link['value'])) {
					$content = new ImageContent($link['value'], false);
					array_push($thumbnails, $content);
				}
			}
		}
		
		return $thumbnails;
	}
	
	/**
	 * \brief Gets all image URI from the link section of the POI object.
	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
	 * @retval array an array containing all the image URI or false.
	 */
	public static function getImagesUri($poi) {
		if(!isset($poi))
			return false;
		
		if(!isset($poi['link']))
			return false;
			
		$images = array();
		$links = $poi['link'];
		foreach($links as $link) {
			if(isset($link['term'])
				&& $link['term'] == DataReader::$term['LINK_TERM_RELATED']
				&& strstr($link['type'], 'image/')) {
				$content = new ImageContent($link['href']);
				array_push($images, $content);
			}
		}
		
		return $images;
	}
	
	/**
	 * \brief Gets the contacts from the POI object.
	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
	 * @retval string the contacts in vCard format or false.
	 */
	public static function getContacts($poi) {
		if(!isset($poi))
			return false;
		
		if(!isset($poi['location']) || (isset($poi['location']) && !isset($poi['location']['address'])))
			return false;
		else
			return $poi['location']['address']['value'];
	}
	
	/**
	 * \brief Gets the calendar with the given term from the POI object.
	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
	 * @param string $term the term given to the desired calendar
	 * @retval string the calendar with the given term in iCalendar format or false.
	 */
	public static function getCalendar($poi, $term) {
		if(!isset($poi))
			return false;
		
		if(array_key_exists('time', $poi)) {
			$times = $poi['time'];
			foreach($times as $time) {
				if($time['type'] == 'text/calendar' && $time['term'] == $term)
					return $time['value'];
			}
		}
		
		return false;
	}
	
	/**
	 * \brief Gets all the locations of the POI object with a given term.
	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
	 * @param string $term the wanted term.
	 * @retval array an array containing all locations with the given term or false
	 */
	public static function getLocations($poi, $term) {
		if(!isset($poi))
			return false;
			
		$points = DataReader::getLocationPoint($poi, $term);
		$lines = DataReader::getLocationLine($poi, $term);
		$polygons = DataReader::getLocationPolygon($poi, $term);
		
		return array_merge($points, $lines, $polygons);
	}
	
	/**
	 * \brief Gets the point from the location of the POI object with a given term.
	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
	 * @param string $term the wanted term.
	 * @retval array an array containing all points with the given term or false
	 */
	public static function getLocationPoint($poi, $term) {
		if(!isset($poi))
			return false;
		
		$list = array();
		$location = $poi['location'];
		if(isset($location['point'])) {
			$points = $location['point'];
			foreach($points as $point) {
				if($point['term'] == $term) {
					$data = explode(' ', $point['Point']['posList']);
					$point = new PointGeometry(new GeometryContent($data[0], $data[1]));
					array_push($list, $point);
				}
			}
		}
	
		return $list;
	}
	
	/**
	 * \brief Gets the lines from the location of the POI object with a given term.
	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
	 * @param string $term the wanted term.
	 * @retval array an array containing all lines with the given term or false
	 */
	public static function getLocationLine($poi, $term) {
		if(!isset($poi))
			return false;
		
		$list = array();
		$location = $poi['location'];
		if(isset($location['line'])) {
			$lines = $location['line'];
			foreach($lines as $line) {
				if($line['term'] == $term) {
					$data = explode(',', $line['LineString']['posList']);
					$point = explode(' ', $data[0]);
					$firstPoint = new GeometryContent($point[0], $point[1]);
					
					$point = explode(' ', $data[1]);
					$secondPoint = new GeometryContent($point[1], $point[2]);
					
					$line = new LineGeometry($firstPoint, $secondPoint);
					array_push($list, $line);
				}
			}
		}
	
		return $list;
	}
	
	/**
	 * \brief Gets the polygons from the location of the POI object with a given term.
	 * @param array $poi a single POI object (such as a Point Of Interest, Route or Event).
	 * @param string $term the wanted term.
	 * @retval array an array containing all polygons with the given term or false
	 */
	public static function getLocationPolygon($poi, $term) {
		if(!isset($poi))
			return false;
		
		$list = array();
		$location = $poi['location'];
		if(isset($location['polygon'])) {
			$polygons = $location['polygon'];
			foreach($polygons as $polygon) {
				if($polygon['term'] == $term) {
					$data = explode(',', $polygon['SimplePolygon']['posList']);
					$polygon = new PolygonGeometry();
					for($i = 0; $i < count($data); $i++) {
						$posList = explode(' ', $data[$i]);
						$polygon->addPoint(new GeometryContent($posList[0], $posList[1]));
					}
					
					array_push($list, $polygon);
				}
			}
		}
	
		return $list;
	}
	
	/**
	 * \brief Gets the relationship base or id with a given term from the POI object.
	 * @param array $poi the object to get the data.
	 * @param string $term the term used.
	 * @param string $field either base or id. Defaults to base.
	 * @retval string the relationship base/id with the given term or false if none was found.
	 */
	public static function getRelationship($poi, $term, $field = 'base') {
		if(!isset($poi) 
			|| ($field != 'base' && $field != 'id'))
			return false;
		
		$location = $poi['location'];
		if(isset($location['relationship'])) {
			$relationships = $location['relationship'];
			foreach($relationships as $relationship) {
				if($relationship['term'] == $term)
					return $relationship[$field];
			}
		}
		
		return false;
	}
	
	/**
	 * \brief Gets a given link with a given term from the POI object.
	 * @param array $poi the object to get the data.
	 * @param array $term the term used - see {@link $term}
	 * @retval string the link with the given term or false if none was found.
	 */
	public static function getLink($poi, $term) {
		if(isset($poi))
			return false;
		
		$links = $poi['link'];
		foreach($links as $link) {
			if(isset($link['term'])
				&& $link['term'] == $term) {
				return $link['href'];
			}
		}
	
		return false;
	}
}

/**
 * \brief Container of an image
 *
 * @author Pedro Cruz
 */
class ImageContent {
	/**
	 * \brief The content of this image
	 */
	public $content;
	/**
	 * \brief Indicates if it is a URI or not
	 */
	public $isUri;
	
	/**
	 * \brief Default constructor.
	 * @param string $content content of image
	 * @param bool $isUri indicates whether the content is an URI or not. Defaults to true.
	 */
	public function __construct($content, $isUri = true) {
		$this->content = $content;
		$this->isUri = $isUri;
	}
}

/**
 * \brief Container of a geometry
 *
 * @author Pedro Cruz
 */
class GeometryContent {
	/**
	 * \brief contains the latitude of the geometry
	 */
	public $latitude;
	/**
	 * \brief contains the longitude of the geometry
	 */
	public $longitude;
	
	/**
	 * \brief Default constructor.
	 * @param float $latitude the latitude of the geometry
	 * @param float $longitude the longitude of the geometry
	 */
	public function __construct($latitude, $longitude) {
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}
}

/**
 * \brief Interface of a geometry
 *
 * @author Pedro Cruz
 */
interface Geometry {
	/**
	 * \brief Returns the size of the implemented geometry
	 */
	public function getSize();
}

/**
 * \brief A point geometry
 *
 * @author Pedro Cruz
 */
class PointGeometry implements Geometry {
	/**
	 * \brief The geometry of this point
	 * Related with {@link GeometryContent}
	 */
	public $geometry;
	
	/**
	 * \brief Default constructor.
	 * @param float $geometry the geometry of the Point
	 */
	public function __construct($geometry) {
		$this->geometry = $geometry;
	}
	
	public function getSize() {
		return 1;
	}
}

/**
 * \brief A line geometry
 *
 * @author Pedro Cruz
 */
class LineGeometry implements Geometry {
	/**
	 * \brief The first point of this line
	 * Related with {@link GeometryContent}
	 */
	public $firstPoint;
	/**
	 * \brief The second point of this line
	 * Related with {@link GeometryContent}
	 */
	public $secondPoint;
	
	/**
	 * \brief Default constructor.
	 * @param GeometryContent $firstPoint the first point of the line
	 * @param GeometryContent $secondPoint the second point of the line
	 */
	public function __construct($firstPoint, $secondPoint) {
		$this->firstPoint = $firstPoint;
		$this->secondPoint = $secondPoint;
	}
	
	public function getSize() {
		return 2;
	}
}

/**
 * \brief A line geometry
 *
 * @author Pedro Cruz
 */
class PolygonGeometry implements Geometry {
	/**
	 * \brief The geometries of this polygon
	 * An array containing {@link GeometryContent}
	 */
	public $polygon;
	
	/**
	 * \brief Default constructor.
	 */
	public function __construct() {
		$this->polygon = array();
	}
	
	/**
	 * \brief Adds a points to the polygon
	 * @param GeometryContent $point the point to be added
	 */
	public function addPoint($point) {
		array_push($this->polygon, $point);
	}
	
	public function getSize() {
		return count($this->polygon);
	}
}
?>