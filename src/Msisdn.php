<?php
/**
 *
 * @category Library
 * @package  BashPH
 * @author   Yan Santos <bybashph@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  GIT: 1.X
 * @link     https://github.com/BashPH/ph-msisdn
 */

declare(strict_types=1);

namespace BashPH;

class Msisdn
{

    /**
     * Load Prefix
     * This will load the prefix from files under prefix directory.
     *
     * @param string $network Telco Identifier.
     *
     * @return array
     */
    protected static function loadPrefix($network) : array
    {
        $network = ('' === $network) ? 'all' : strtolower($network);
        $networks = array('all','globe','tm','smart','sun','tnt');
        if (!in_array(strtolower($network), $networks)) {
            return [
                'valid' => false,
                'error' => 'Invalid mobile network',
                'available-network' => $networks
            ];
        }
        return include dirname(__FILE__) . '/prefix/' . $network . '.php';
    }

    /**
     * List Prefix
     * Static function to list prefix using loadPrefix.
     *
     * @param string $network Telco Identifier.
     *
     * @return array
     */
    public static function listPrefix($network = '') : array
    {
        return Msisdn::loadPrefix($network);
    }

    /**
     * Check Prefix
     * Static function to check msisdn prefix.
     *
     * @param string $msisdn Mobile number to check.
     *
     * @return array
     */
    public static function getPrefix($msisdn) : array
    {

        $msisdn = Msisdn::clean($msisdn);
        $msisdnLength = strlen($msisdn);
        if ($msisdnLength === 10) {
            $prefix = substr($msisdn, 0, 3);
        } elseif ($msisdnLength === 11) {
            $prefix = substr($msisdn, 0, 4);
        } else {
            return [
                'valid' => false,
                'error' => 'Mobile number length should be 10 to 11 excluding prefix'
            ];
        }
        return [
            'valid' => true,
            'prefix' => $prefix
        ];
    }

    /**
     * Check Telco
     * Static function to retrieve mobile number carrier.
     *
     * @param string $msisdn Mobile number to check.
     *
     * @return string
     */
    public static function getTelco($msisdn) : string
    {

        $msisdn = Msisdn::validate($msisdn);
        if (false === $msisdn['valid']) {
            return 'invalid';
        } else {
            if (null !== $msisdn['carrier']['other']) {
                return $msisdn['carrier']['network'] . '/' . $msisdn['carrier']['other'];
            }
            return $msisdn['carrier']['network'];
        }
    }

    /**
     * Format
     * Format the mobile number.
     *
     * @param string     $msisdn      Mobile number to be formatted.
     * @param bool|false $countryCode True to use country code, otherwise false.
     * @param string|''  $separator   The seperator for msisdn.
     *
     * @return string
     */
    public static function format($msisdn, $countryCode = false, $separator = '') : string
    {
        $msisdn = Msisdn::clean($msisdn);

        $prefix = strlen($msisdn) - 7;
        $msisdn = substr_replace($msisdn, $separator, $prefix, 0);
        $msisdn = substr_replace($msisdn, $separator, $prefix + 4, 0);

        if ($countryCode) {
            $msisdn = '+63' . $separator . $msisdn;
        } else {
            $msisdn = '0' . $msisdn;
        }
        
        return $msisdn;
    }

    /**
     * Sanitize
     * Removes none numeric character.
     *
     * @param string $msisdn Mobile Number to be sanitized.
     *
     * @return string
     */
    public static function sanitize($msisdn) : string
    {
        return preg_replace('/[^0-9]/', '', $msisdn);
    }

    /**
     * Remove Leading Zero
     * Removes the leading zero from msisdn.
     *
     * @param string $msisdn Mobile Number to remove leading zero.
     *
     * @return string
     */
    public static function removeLeadingZero($msisdn) : string
    {
        $msisdn = Msisdn::sanitize($msisdn);
        $msisdn = substr($msisdn, 1, strlen($msisdn));
        return ($msisdn) ? $msisdn : '';
    }

    /**
     * Remove Country Code
     * Removes country code from msisdn.
     *
     * @param string $msisdn Mobile Number to remove country code.
     *
     * @return string
     */
    public static function removeCountryCode($msisdn) : string
    {
        $msisdn = Msisdn::sanitize($msisdn);
        $msisdn = substr($msisdn, 2, strlen($msisdn));
        return ($msisdn) ? $msisdn : '';
    }
    
    /**
     * Is Empty
     * Check if msisdn is empty.
     *
     * @param string $msisdn Mobile Number check if empty.
     *
     * @return bool
     */
    public static function isEmpty($msisdn) : bool
    {
        return empty($msisdn);
    }
    
    /**
     * Is Number
     * Check if msisdn is a number.
     *
     * @param string $msisdn Mobile Number to check if number.
     *
     * @return bool
     */
    public static function isNumber($msisdn) : bool
    {
        return is_numeric($msisdn);
    }

    /**
     * Clean
     * Clean the msisdn by using sanitize, removeLeadingZero or removeCountryCode.
     *
     * @param string $msisdn Mobile Number to clean.
     *
     * @return string
     */
    public static function clean($msisdn) : string
    {

        $msisdn = Msisdn::sanitize($msisdn);

        if (substr($msisdn, 0, 1) === '0') {
            $msisdn = Msisdn::removeLeadingZero($msisdn);
        } else {
            if (substr($msisdn, 0, 2) === '63') {
                $msisdn = Msisdn::removeCountryCode($msisdn);
            }
        }

        return $msisdn;
    }

    /**
     * Validate
     * Validates the msisdn, optional network to check
     * if valid from certain network.
     *
     * @param string $msisdn    Mobile Number to validate.
     * @param string    $network   Telco Identifier.
     * @param string $separator Seperator for msisdn format.
     *
     * @return array
     */
    public static function validate($msisdn, $network = '', $separator = '') : array
    {

        if (true === Msisdn::isEmpty($msisdn)) {
            return [
                'valid' => false,
                'error' => 'Mobile number cannot be empty'
            ];
        }
        
        if (false === Msisdn::isNumber($msisdn)) {
            return [
                'valid' => false,
                'error' => 'Mobile number must be numbers'
            ];
        }
        
        $msisdn = Msisdn::clean($msisdn);

        $msisdnLength = strlen($msisdn);

        $prefix = false;

        if ($msisdnLength === 10) {
            $prefix = substr($msisdn, 0, 3);
        } elseif ($msisdnLength === 11) {
            $prefix = substr($msisdn, 0, 4);
        } else {
            return [
                'valid' => false,
                'error' => 'Mobile number length should be 10 to 11 including carrier prefix'
            ];
        }

        $prefixList = Msisdn::listPrefix($network);
        
        if ((isset($prefixList['valid'])) and (false === $prefixList['valid'])) {
            return $prefixList;
        }
        if (isset($prefixList[$prefix]) || array_key_exists($prefix, $prefixList)) {
            return [
                'valid' => true,
                'prefix' => $prefix,
                'carrier' => $prefixList[$prefix],
                'format' => [
                    Msisdn::format($msisdn, true, $separator),
                    Msisdn::format($msisdn, false, $separator),
                ],
                'error' => null
            ];
        } else {
            if ($network === '') {
                return [
                    'valid' => false,
                    'error' => 'Mobile number prefix is not valid',
                    'prefix-sent' => $prefix
                ];
            } else {
                $network = strtoupper($network);
                return [
                    'valid' => false,
                    'error' => 'Mobile number is not a ' . $network . ' number',
                    'prefix-sent' => $prefix
                ];
            }
        }
    }

    /**
     * Is Valid
     * Validates msisdn using validate but with simple response.
     *
     * @param string $msisdn Mobile Number to validate.
     *
     * @return bool
     */
    public static function isValid($msisdn = null) : bool
    {
        return Msisdn::validate($msisdn, 'all')['valid'];
    }
    
    /**
     * Is Valid Globe
     * Validates msisdn for Globe using validate but with simple response.
     *
     * @param string $msisdn Mobile Number to validate.
     *
     * @return bool
     */
    public static function isValidGlobe($msisdn = null) : bool
    {
        return Msisdn::validate($msisdn, 'globe')['valid'];
    }
    
    /**
     * Is Valid TM
     * Validates msisdn for TM using validate but with simple response.
     *
     * @param string $msisdn Mobile Number to validate.
     *
     * @return bool
     */
    public static function isValidTM($msisdn = null) : bool
    {
        return Msisdn::validate($msisdn, 'tm')['valid'];
    }
    
    /**
     * Is Valid Smart
     * Validates msisdn for Smart using validate but with simple response.
     *
     * @param string $msisdn Mobile Number to validate.
     *
     * @return bool
     */
    public static function isValidSmart($msisdn = null) : bool
    {
        return Msisdn::validate($msisdn, 'smart')['valid'];
    }
    
    /**
     * Is Valid Sun
     * Validates msisdn for Sun using validate but with simple response.
     *
     * @param string $msisdn Mobile Number to validate.
     *
     * @return bool
     */
    public static function isValidSun($msisdn = null) : bool
    {
        return Msisdn::validate($msisdn, 'sun')['valid'];
    }
    
    /**
     * Is Valid TNT
     * Validates msisdn for TNT using validate but with simple response.
     *
     * @param string $msisdn Mobile Number to validate.
     *
     * @return bool
     */
    public static function isValidTnT($msisdn = null) : bool
    {
        return Msisdn::validate($msisdn, 'tnt')['valid'];
    }
}
