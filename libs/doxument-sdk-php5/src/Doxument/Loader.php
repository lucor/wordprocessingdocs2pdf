<?php
/**
 * Doxument REST API Client PHP5 Library
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * @category Doxument
 * @author $Author: support@doxument.com $
 * @copyright Copyright (c) 2011 Doxument.com (http://www.doxument.com)
 * @license http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @link http://www.doxument.com
 * @version $Date: 2012-04-16 10:38:48 +0300 (Mon, 16 Apr 2012) $
 */

class Doxument_Loader {

    /**
     * spl_autoload implementation
     * 
     * @param string $classname
     * @return void
     */
    public static function autoload($classname) {
    	$filename = str_replace('_', '/', $classname).'.php';
    	require_once $filename;
    }
}
