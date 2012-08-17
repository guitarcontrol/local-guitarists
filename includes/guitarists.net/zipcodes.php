<?php
/**
 * Class to find zip codes within an approximate
 * distance of another zip code. This can be useful
 * when trying to find retailers within a certain
 * number of miles to a customer.
 *
 * This class makes some assumptions that I consider
 * pretty safe.  First it assumes there is a database
 * that houses all of the zip code information. 
 * Secondly it assumes there is a way to validate a
 * zip code for a given country.  It makes one bad
 * assumption and that is that the world is flat. See
 * comments below for an explanation.
 *
 * @author  Scott Mattocks
 * @created 2004-05-03
 * @updated 2004-05-14
 */

require_once ('DB.php');
define ('DSN', 'mysql://gnet_db:crlPUbN7-k@A@localhost:3306/gnet_guitarists');

class ZipCodesRange {

  /**
   * The conversion factor to go from miles to degrees.
   * @var float
   */
  var $milesToDegrees = .01445;
  /**
   * The zipcode we are starting from.
   * @var string
   */
  var $zipCode;
  /**
   * The maximum distance in miles to return results for.
   * @var float
   */
  var $range;
  /**
   * The country the zip code is in.
   * @var string Two character ISO code.
   */
  var $country;  
  /**
   * The result of our search.
   * array(zip1 => distance, zip2 =>distance,...)
   * @var array
   */
  var $zipCodes = array ();
  /**
   * The database table to look for zipcodes in.
   * @var string
   */
  var $dbTable = 'zipcode';
  /**
   * The name of the column containing the zip code.
   * @var string
   */
  var $dbZip = 'zip';
  /**
   * The name of the column containing the longitude.
   * @var string
   */
  var $dbLon = 'longitude';
  /**
   * The name of the column containing the latitude.
   * @var string
   */
  var $dbLat = 'latitude';
  /**
   * The array we store our search options in.
   * @var string
   */
  var $arrOptions = array();

  /**
   * Constructor. Calls initialization method.
   *
   * @access private
   * @param  string  $zipCode
   * @param  float   $range
   * @param  string  $country Optional. Defaults to US.
   * @return object
   */
  function ZipCodesRange($zipCode, $range, $country = 'US', $arrOptions) {
    
    $this->_initialize($zipCode, $range, $country, $arrOptions);
  }

  /**
   * Initialization method. 
   * Checks data and sets member variables.
   *
   * @access private
   * @param  string  $zipCode
   * @param  float   $range
   * @param  string  $country Optional. Defaults to US.
   * @return boolean 
   */   
  function _initialize($zipCode, $range, $country, $arrOptions) {
    
    // Check the country.
    if ($this->validateCountry($country)) {
      $this->country = $country;
    } else {
      trigger_error('Invalid country: ' . $country);
      return FALSE;
    }

    // Check the zipcode.
    if ($this->validateZipCode($zipCode, $country)) {
      $this->zipCode = $zipCode;
    } else {
      trigger_error('Invalid zip code: ' . $zipCode);
      return FALSE;
    }

    // We don't need a special method to check the range.
    if (is_numeric($range) && $range >= 0) {
      $this->range = $range;
    } else {
      trigger_error('Invalid range: ' . $range);
      return FALSE;
    }
    
    // setour options array
    $this->arrOptions = $arrOptions;
    
    // Set up the zip codes.
    return $this->setZipCodesInRange();
  }

  /**
   * Get all of the zip codes from the database.
   * Currently this method is called on construction but
   * it doesn't have to be.
   *
   * @access public
   * @param  none
   * @return boolean
   */
  function setZipCodesInRange() {
    
    // First check that everything is set.
    if (!isset($this->zipCode) || !isset($this->range) || !isset($this->country)) {
      trigger_error('Cannot get zip codes. Class not initialized properly.');
      return FALSE;
    }

    // Get the max longitude and latitude of the starting point.
    $maxCoords = $this->getRangeBox();
    
    // set our default where clause
    $where = "";
    
    // see if they chose their experience
    if ($this->arrOptions["experience"]) {
        $where .= "about.intExperience = '" . $this->arrOptions["experience"] . "' AND ";
    }
    
    // see if they chose a song type
    if ($this->arrOptions["songs"]) {
        $where .= "about.intSongTypes = '" . $this->arrOptions["songs"] . "' AND ";
    }
    
    // see if they chose a song type
    if ($this->arrOptions["situation"]) {
        $where .= "about.intSituation = '" . $this->arrOptions["situation"] . "' AND ";
    }
    
    // see if we need to loop through their influences
    if (count($this->arrOptions["influences"])) {
        $where .= " ( MATCH(txtInfluences) AGAINST ('123XYZ') ";
        
        // loop through each influence and create our match text
        foreach ($this->arrOptions["influences"] as $influence) {
            $where .= "OR MATCH(txtInfluences) AGAINST ('" . $influence . "') ";
        }
        
        $where .= " ) AND ";
    }
    
    // The query.
    $query = "
        SELECT    DISTINCT(members.strUsername) as strUsername,
                  zipcode.zip,
                  zipcode.latitude,
                  zipcode.longitude,
                  members.ID,
                  members.intAccess,
                  members.strAccess,
                  members.dateLVisit,
                  about.strCity,
                  about.intState,
                  about.strState,
                  about.intCountry,
                  about.intPlayYears,
                  about.txtInfluences
        FROM      zipcode,
                  members,
                  about,
                  member_styles
        WHERE     (zipcode.latitude <= " . $maxCoords['max_lat'] . " AND zipcode.latitude >= " . $maxCoords['min_lat'] . ") AND 
                  (zipcode.longitude <= " . $maxCoords['max_lon'] . " AND zipcode.longitude >= " . $maxCoords['min_lon'] . ") AND
                  zipcode.zip = about.strZipCode AND
                  $where
                  about.intMemID = member_styles.memid AND
                  member_styles.styleid IN ( " . implode(", ", $this->arrOptions["styles"]) . " ) AND
                  member_styles.memid = members.ID AND
                  members.intPrivate = 0";
    
    // Query the database.
    $dbConn =& DB::connect(DSN);
    $result = $dbConn->query($query);
    
    // Check for errors.
    if (DB::isError($result)) {
      trigger_error('Database error: ' . $result->getMessage . ' ' . $query, E_USER_ERROR);
    }

    // Process each row.
    while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {

      // Get the distance form the origin (imperfect see below).
      $distance = $this->calculateDistance($row[$this->dbLat], $row[$this->dbLon]);
      // Double check that the distance is within the range.
      if ($distance < $this->range) {
	// Add the zip to the array
	//$this->zipCodes[$row[$this->dbZip]] = $distance;
        $this->zipCodes[] = array($row["zip"],
                                  $row["ID"],
                                  $row["strUsername"],
                                  $row["intAccess"],
                                  $row["strAccess"],
                                  $row["dateLVisit"],
                                  $row["strCity"],
                                  $row["intState"],
                                  $row["strState"],
                                  $row["intPlayYears"],
                                  $row["txtInfluences"],
                                  $distance);
      }
      
      // reorder the array, based on mileage
      
    }
    return TRUE;
  }

  /**
   * Return the array of results.
   *
   * @access public
   * @param  none
   * @return &array
   */
  function &getZipCodesInRange() {
    return $this->zipCodes;
  }

  /**
   * Calculate the distance from the coordinates are from the
   * origin zip code.
   *
   * The method is quite imperfect.  It assumes as flat Earth.
   * The values are quite accurate (depending on the conversion
   * factor used) for zip codes close  to the equator. I found
   * some crazy formula for calulating distance on a sphere
   * but I am not good enough at calculus to convert that into
   * working code.
   *
   * @access public
   * @param  float $lat       The latitude you want to know the distance to.
   * @param  float $lon       The longitude you want to know the distance to.
   * @param  float $zip       The zip code you want to know the distance from.
   * @param  int   $percision The number of decimals places in the distance.
   * @return float            The distance from the zip code to the coordinates.
   */
  function calculateDistance($lat, $lon, $zip = NULL, $percision = 2) {
    
    // Check the zip first.
    if (!isset ($zip)) {
      // Make it default to the origin zip code.
      // Could be used to calculate distances from other points.
      $zip = $this->zipCode;
    }
    // Get the coordinates of our starting zip code.
    list ($starting_lon, $starting_lat) = $this->getLonLat($zip);

    // Get the difference in miles for both coordinates.
    $diffLonMiles = ($starting_lon - $lon) / $this->milesToDegrees;
    $diffLatMiles = ($starting_lat - $lat) / $this->milesToDegrees;
    
    // Calculate the distance between two points.
    $distance = sqrt(($diffLonMiles * $diffLonMiles) + ($diffLatMiles * $diffLatMiles));

    // Return the distance rounded to the defined percision.
    return round($distance, $percision);
  }

  /**
   * Get the longitude and latitude for a single zip code.
   *
   * @access public
   * @param  string $zip  The zipcode to get the coordinates for.
   * @return array  Numerically index with longitude first.
   */
  function getLonLat($zip) {
    
    // Get the longitude and latitude for the zip code.
    $query = 'SELECT ' . $this->dbLon . ', ' . $this->dbLat . ' ';
    $query.= 'FROM ' . $this->dbTable . ' ';
    $query.= 'WHERE ' . $this->dbZip . ' = \'' . addslashes($zip) . '\' ';

    $dbConn =& DB::connect(DSN);

    return $dbConn->getRow($query);
  }

  /**
   * Check to see if the country is valid.
   *
   * Not implemented in any useful manner.
   *
   * @access public
   * @param  string  $country The country to check.
   * @return boolean
   */
  function validateCountry($country) {

    return (strlen($country) == 2);
  }

  /**
   * Check to see if a zip code is valid.
   *
   * Not implemented in any useful manner.
   *
   * @access public
   * @param  string $zip     The code to validate.
   * @param  string $country The country the zip code is in.
   * @return boolean
   */
  function validateZipCode($zip, $country = NULL) {
    
    // Set the country if we need to.
    if (!isset($country)) {
      $country = $this->country;
    }
    
    // There should be a way to check the zip code for every
    // acceptabe country.
    return TRUE;
  }

  /**
   * Get the maximum and minimum longitude and latitude values
   * that our zip codes can be in.
   *
   * Not all zipcodes in this box will be with in the range.
   * The closest edge of this box is exactly range miles away
   * from the origin but the corners are sqrt(2(range^2)) miles
   * away. That is why we have to double check the ranges.
   *
   * @access public
   * @param  none
   * @return &array The edges of the box.
   */
  function &getRangeBox() {

    // Calculate the degree range using the mile range
    $degrees = $this->range * $this->milesToDegrees;

    // Get the coords for our starting zip code.
    list($starting_lon, $starting_lat) = $this->getLonLat($this->zipCode);
    
    // Set up an array to return.
    $ret_array = array ();

    // Lat/Lon ranges 
    $ret_array['max_lat'] = $starting_lat + $degrees;
    $ret_array['max_lon'] = $starting_lon + $degrees;
    $ret_array['min_lat'] = $starting_lat - $degrees;
    $ret_array['min_lon'] = $starting_lon - $degrees;

    return $ret_array;
  }

  /**
   * Allow users to set the name of the database table holding
   * the information.
   *
   * @access public
   * @param  string $table The name of the db table.
   * @return void
   */
  function setTableName($table) {
    $this->dbTable = $name;
  }

  /**
   * Allow users to set the name of the column holding the
   * latitude value.
   *
   * @access public
   * @param  string $lat The name of the column.
   * @return void
   */
  function setLatColumn($lat) {
    $this->dbLat = $lat;
  }

  /**
   * Allow users to set the name of the column holding the
   * longitude value.
   *
   * @access public
   * @param  string $lon The name of the column.
   * @return void
   */
  function setLonColumn($lon) {
    $this->dbLon = $lon;
  }

  /**
   * Allow users to set the name of the column holding the
   * zip code value.
   *
   * @access public
   * @param  string $zips The name of the column.
   * @return void
   */
  function setZipColumn($zip) {
    $this->dbZip = $zip;
  }

  /**
   * Set a new origin and re-get the data.
   *
   * @access public
   * @param  string $zip The new origin.
   * @return void
   */
  function setNewOrigin($zip) {
    
    if ($this->validateZipCode($zip)) {
      $this->zipCode = $zip;
      $this->setZipCodesInRange();
    }
  }
  
  /**
   * Set a new range and re-get the data.
   *
   * @access public
   * @param  float  $range The new range.
   * @return void
   */
  function setNewRange($range) {
    
    if (is_numeric($range)) {
      $this->range = $range;
      $this->setZipCodesInRange();
    }
  }

  /**
   * Set a new country but don't re-get the data.
   * 
   * It isn't any good to check a zip code in two 
   * countries cause the rules for zip codes vary from
   * country to country.
   *
   * @access public
   * @param  string $country The new country.
   * @return void
   */
  function setNewCountry($coutry) {

    if ($this->validateCountry($country)) {
      $this->country = $country;
    }
  }

  /**
   * Allow users to set the converstion ratio.
   * Hopefully you are changing the percision
   * and not setting a bad value.
   *
   * @access public
   * @param  float  $rate The new value.
   * @return void
   */
  function setConversionRate($rate) {
    
    if (is_numeric($rate)) {
      $this->milesToDegrees = $rate;
    }
  }
}
// Debugging lines
/*$zcr = new ZipCodesRange(21875, 10);
print_r($zcr);
*/
?>
