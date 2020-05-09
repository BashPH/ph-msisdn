# PH Msisdn

[![Build Status](https://travis-ci.com/BashPH/ph-msisdn.svg?branch=1.x)](https://travis-ci.com/BashPH/ph-msisdn) 
[![Packagist Version](https://img.shields.io/packagist/v/bashph/ph-msisdn?label=stable)](https://packagist.org/packages/bashph/ph-msisdn) 
[![GitHub](https://img.shields.io/github/license/bashph/ph-msisdn)](https://github.com/BashPH/ph-msisdn/blob/1.x/LICENSE) 


Simple static PHP validation for Philippine mobile numbers.

### Features

* Support for latest prefixes (4 digit prefixes, ie: 09173).
* Validate Philippine mobile numbers.
* Validate by mobile carrier.
* Format mobile numbers.
* Clean/Sanitize mobile numbers.
* List all prefixes or list by carrier. 
* Get prefix from mobile number and many more...

### Requirements

* PHP ^5.6 or PHP ^7.0
* PHP ^7.3 (optional, to run PHPUnit)
* PHPUnit (optional,to run tests)

### Installation

Use composer to install PH Msisdn.

```bash
composer require bashph/ph-msisdn
```

### Usage

```php
use BashPH\Msisdn;


$number = '09177654321';

// Validates mobile number
if (true === Msisdn::isValid($number)) {
    // valid mobile number.
} else {
    // invalid mobile number.
}

// Validates mobile number and check if it belongs to certain carrier.
if (true === Msisdn::isValidGlobe($number)) {
    // valid mobile number and belongs to certain carrier.
} else {
    // invalid mobile number and/or it does not belongs to certain carrier.
}

```

### Using Validate()
The advantage of using validate() is that it returns an array of details about the mobile number, that includes the carrier, the prefix, formatted mobile number and it also returns the error if mobile number is invalid.

```php
use BashPH\Msisdn;


$number = '09177654321';

/**
 * Validates mobile number
 * validate() returns array
 */
$msisdn = Msisdn::validate($number);

if(true === $msisdn['valid']) {

    $prefix = $msisdn['prefix'];                           // "917"
    $network = $msisdn['carrier']['network'];              // "Globe"
    $formatWithCountryCode = $msisdn['format'][0];         // "+639177654321"
    $formatWithLeadingZero = $msisdn['format'][1];         // "09177654321"

} else {
    $error = $msisdn['error'] // Error why mobile number is not valid.
}

```

### Available Functions

#### isValid()
```php
// Validates mobile number if valid.
// This function use Msisdn::validate($number, 'all').
Msisdn::isValid($number) // returns bool (true or false)
```

#### isValidGlobe()
```php
// Validates mobile number if valid and if belongs to Globe.
// This function use Msisdn::validate($number, 'globe')
Msisdn::isValidGlobe($number) // returns bool (true or false)
```

#### isValidTM()
```php
// Validates mobile number if valid and if belongs to TM.
// This function use Msisdn::validate($number, 'tm')
Msisdn::isValidTM($number) // returns bool (true or false)
```

#### isValidSmart()
```php
// Validates mobile number if valid and if belongs to Smart.
// This function use Msisdn::validate($number, 'smart')
Msisdn::isValidSmart($number) // returns bool (true or false)
```

#### isValidSun()
```php
// Validates mobile number if valid and if belongs to Sun.
// This function use Msisdn::validate($number, 'sun')
Msisdn::isValidSun($number) // returns bool (true or false)
```

#### isValidTnT()
```php
// Validates mobile number if valid and if belongs to TnT.
// This function use Msisdn::validate($number, 'tnt')
Msisdn::isValidTnT($number) // returns bool (true or false)
```

#### validate()
```php
/**
 *
 * Validate does the ff:
 * 1.) it checks if mobile number provided is null using Msisdn::isNull().
 * 2.) it checks if mobile number is empty using Msisdn::isEmpty().
 * 3.) it checks if mobile number if numeric (including some symbols) using Msisdn::isNumber()
 * 4.) it cleans the mobile number using Msisdn::clean().
 * 5.) it validates the length (numbers with prefix should be between 10 to 11).
 * 6.) it loads the prefix using Msisdn::listPrefix().
 * 7.) it checks if prefix with the loaded prefix.
 * 8.) it create an array for response, uses Msisdn::format to format mobile number.
 */

// example using valid number.
Msisdn::validate('091737654321') // returns array

// response
array:5 [
  "valid" => true
  "prefix" => "9173"
  "carrier" => array:3 [
    "network" => "Globe"
    "other" => null
    "type" => "Postpaid"
  ]
  "format" => array:2 [
    0 => "+6391737654321"
    1 => "091737654321"
  ]
  "error" => null
]

// example using invalid number
Msisdn::validate('091737654321345') // returns array

//response 
array:2 [
  "valid" => false
  "error" => "Mobile number length should be 10 to 11 excluding prefix"
]

```
#### Other parameters for validate()
```php

// validate and check if mobile number belongs to carrier.
// available carriers are 'all', 'globe', 'tm', 'smart', 'tnt', 'sun'.

Msisdn::validate('091737654321','globe') // returns array


// validate and add a separator for mobile number format.
// add false in second parameter if you don't need to validate the carrier.

Msisdn::validate('091737654321',false,'-') // returns array

// response
array:5 [
  "valid" => true
  "prefix" => "9173"
  "carrier" => array:3 [
    "network" => "Globe"
    "other" => null
    "type" => "Postpaid"
  ]
  "format" => array:2 [
    0 => "+63-9173-765-4321"
    1 => "09173-765-4321"
  ]
  "error" => null
]

// validate and check if mobile number belongs to carrier then add a separator.

Msisdn::validate('091737654321','globe','.') // returns array

// response
array:5 [
  "valid" => true
  "prefix" => "9173"
  "carrier" => array:3 [
    "network" => "Globe"
    "other" => null
    "type" => "Postpaid"
  ]
  "format" => array:2 [
    0 => "+63.9173.765.4321"
    1 => "09173.765.4321"
  ]
  "error" => null
]
```

#### sanitize()
```php
// Sanitize the mobile number removing all characters except numbers.
Msisdn::sanitize($number) // returns string
```

#### removeLeadingZero()
```php
// Remove leading zero from the mobile number.
// This function use Msisdn::sanitize($number)
Msisdn::removeLeadingZero($number) // returns string
```

#### removeCountryCode()
```php
// Remove country code from mobile number.
// This function use Msisdn::sanitize($number)
Msisdn::removeCountryCode($number) // returns string
```
#### isNull()
```php
// Check if mobile number is null.
Msisdn::isNull($number) // returns bool (true or false)
```

#### isEmpty()
```php
// Check if mobile number is empty.
Msisdn::isEmpty($number) // returns bool (true or false)
```

#### isNumber()
```php
// Check if mobile number is numeric.
Msisdn::isNumber($number) // returns bool (true or false)
```

#### clean()
```php
// Clean the mobile number
// This function use Msisdn::sanitize($number)
// This function use Msisdn::removeLeadingZero($number)
// This function use Msisdn::removeCountryCode($number)
Msisdn::clean($number) // returns string
```

#### getPrefix()
```php
// Get the prefix of mobile number
// This function use Msisdn::clean($number)
Msisdn::getPrefix($number) // returns string
```

#### format()
```php
// Format the mobile number
// This function use Msisdn::clean($number)

$number = '9173-765-4321';

Msisdn::format($number) // returns "091737654321" (string)

// add true to return with country code
Msisdn::format($number,true) // returns "+6391737654321" (string)

// use false if you don't need a country code and add another param for separator
Msisdn::format($number,false, '-') // returns "09173-765-4321" (string)

// or use both
Msisdn::format($number,true, '.') // returns "+63.9173.765.4321" (string)
```

#### listPrefix()
```php
/**
 * List all available prefix
 * Available $prefix are "all", "globe", "tm", "smart", "sun", "tnt"
 * prefix are optional
 */
Msisdn::listPrefix() // returns array of all prefix

Msisdn::listPrefix('globe') // returns array of globe prefix
Msisdn::listPrefix('tm') // returns array of globe prefix
Msisdn::listPrefix('smart') // returns array of globe prefix
Msisdn::listPrefix('sun') // returns array of globe prefix
Msisdn::listPrefix('tnt') // returns array of globe prefix

```

### Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://github.com/BashPH/ph-msisdn/blob/1.x/LICENSE)