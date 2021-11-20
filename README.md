<h3 align="center">Amount of money Converted from Bin cards</h3>

---

<p align="justify"> Our project consists to retrieve the amounts of currencies based on user bin card, amount to be converted and the currency.
    <br> 
</p>

## Table of Contents


- [Table of Contents](#table-of-contents)
- [Application](#application)
  - [Operations classes](#operations-classes)
  - [Used API](#used-api)
    - [🏁 binlist.net API](#-binlistnet-api)
    - [🏁 exchangeratesapi.io API](#-exchangeratesapiio-api)
  - [Implementation of the app](#implementation-of-the-app)
  - [Execution of the app](#execution-of-the-app)
- [Testing the PHP code with PHPUnit](#testing-the-php-code-with-phpunit)
  - [What is PHPUnit?](#what-is-phpunit)
  - [Installation of PHPUnit?](#installation-of-phpunit)
  - [Configuration of PHPUnit?](#configuration-of-phpunit)
  - [Creation of test code](#creation-of-test-code)
  - [The result of testing](#the-result-of-testing)
- [✍️ Authors](#️-authors)

## Application

1. As an input we have a text file contains JSON lines. Each line contains an operation.
2. We create an object to doing all operations.
3. We create an application file.
4. We install all required dependencies to doin a test with PHPUnit.
5. We create a test file for PHPUnit.

### Operations classes

We call our class file **appObject.php**.
The name of the class is currency.
The main method is **results($inputFile)**

```
    /**
     * Print the results of the manipulation on currencies
     * @param string $inputFile input file name
     */
    public function results($inputFile){…}
```

```
    /**
     * Check the input file and extact json lines as an array of json
     * @param string $inputFile input file name
     * @return array $jsonArray (array of the json lines)
     * @return bool If the format or the input file name is incorrect we return false
     */
    public function extractJsonFromFile($inputFile){…}
```

```
    /**
     * Verifiy the bin from binlist API
     * @param string $bin Bin code (the first numbers of the card)
     * @return array $r (Json object)
     */
    public function binDetails($bin){…}
```

```
/**
     * Test if the country is european
     * @param string $countryCode (Code of country on 2 uppercase letters)
     * @return string (yes or no)
     */
    public function isEu($countryCode) {…}
```

```
    /**
     * Extract Amount from the APIexchangeratesapi.io
     * @param string $currency The cuurency on 3 uppercase letters
     * @param double $amount Ammount
     * @return double $amntFixed calculated amount
     */
    public function extractAmount($currency,$amount) {…}
```

### Used API

#### 🏁 binlist.net API

**[binlist.net](https://binlist.net)** is a public web service for looking up credit and debit card meta data<br>

<h6>Limits : </h6><br>
Requests are throttled at 10 per minute with a burst allowance of 10. If you hit the speed limit the service will return a 429 http status code.<br>
<h6>IIN / BIN</h6><br>
The first 6 or 8 digits of a payment card number (credit cards, debit cards, etc.) are known as the Issuer Identification Numbers (IIN), previously known as Bank Identification Number (BIN). These identify the institution that issued the card to the card holder.<br>
<h6>Data</h6><br>
The data backing this service is not a table of card number prefixes. That would be unreliable and provide you with too little information. The data is sourced from multiple places, filtered, prioritized, and combined to form the data you eventually see. Some data is formed based on assumptions we make by looking at adjoining cards.<br>
Although this service is very accurate, don't expect it to be perfect.<br>
<h6>The call </h6><br>
```
    https://lookup.binlist.net/(The Bin Code)
```

#### 🏁 exchangeratesapi.io API

**[exchangeratesapi.io](http://exchangeratesapi.io)** is a free, easy-to-use REST API interface delivering worldwide stock market data in JSON format.<br>
Exchange Rates API began as a simple REST API to allow developers to add currency exchange rate data to their applications. The API has been a trusted data source for millions of developers around the world.<br>
ExchangeRatesApi.io is now proudly part of apilayer, a company that provides developers with powerful data and conversion APIs.<br>
Exchange Rates API provides access to 170 global currencies and over 14,000 exchange rate conversion pairs. Both historical and real-time exchange rates are available with a data refresh rate as often as 60 seconds. Time-series and fluctuation data are also available based on your specific needs.<br>

```
    http://api.exchangeratesapi.io/latest?access_key=(Your acces key)
```

### Implementation of the app

To implement the subject we create **app.php** file.<br>
We call our class from **appObject.php**.

```
require 'appObject.php';
```

### Execution of the app

```
$ php app.php input.txt
```

The result on the screen is like this because the amounts of currencies changes:

```
1
0.44325219454162
1.5549561061548
2.3049114116164
47.676202304429
```
## Testing the PHP code with PHPUnit

### What is PHPUnit?

PHPUnit is a programmer-oriented testing framework for PHP. It is an instance of the xUnit architecture for unit testing frameworks.

### Installation of PHPUnit?

- We use composer to install it.
- We create a file **composer.json** on the top of our project.

```
{
    "require-dev": {
      "phpunit/phpunit": "^8"
    }
}
```
- We install PHPUnit with composer.

```
$ composer require –dev phpunit/phpunit ^8
```
- At the end of the installation we see a successful message on the command line.

<p align="center">
![installation_PHPUnit](https://github.com/azzedinedev/currencyBinConverter/blob/main/assets/phpunit-installation.png)
</p>


### Configuration of PHPUnit?

We use a configuration file to configure PHPUnit. Here we usse colors attrubute as true.

```
<?xml version="1.0" encoding="utf-8" ?>
<phpunit colors="true">
    <testsuite name="Mes super tests">
        <directory>tests</directory>
    </testsuite>
</phpunit>
```

### Creation of test code

We create a folder called « tests » and create a testing file “currencyTest.php”
To write a test, all you have to do is create a class which inherits from the **PHPUnit/Framework/TestCase** class and which will contain methods starting with test. We include also our class currency from appObject.php.

```
require "appObject.php";
use PHPUnit\Framework\TestCase as TestCase;

class currencyTest extends TestCase
{…}
```

We create our test on methods.

```
    /**
     * Check if the file exist or not
     * the file "input.txt" exist / Here we are created 2 assertions
     */
    public function testFileExist(){…}

    /**
     * extractJsonFromFile return an array if the input file exist and his json format is checked as true
     */
    public function testInputAsArray(){…}

    /**
     * A bad format on existing file input it is not an array (return false)
     */
    public function testInputIsNotArray(){…}
    /**
     * A bad format on existing file input it is not an array (return false)
     */
    public function testBadJsonInput(){…}
    
    /**
     * Count of array is 5 (file exist on good format)
     */
    public function testArrayCount(){{…}

    /**
     * Check the object of the bin extracted from the API
     * the results must be an object
     */
    public function testBinDetails(){…}
        
    /**
     * Check country code as european
     */
    public function testIsEuropean(){…}
    
    /**
     * Check country code as not european
     */
    public function testIsNotEuropean(){…}
    
    /**
     * Check the amount
     */
    public function testExtractAmount(){…}

    /**
     * Check the final result
     * the results are stored on an array being 5 elements
     */
    public function testArrayOfResult(){…}
```

### The result of testing

If all it’s OK, the result show at the end **OK (10 tests, 11 assertions)** like this.

```
$ vendor/bin/phpunit
PHPUnit 8.5.21 by Sebastian Bergmann and contributors.

..........                                                        10 / 10 (100%)1
0.44325219454162
1.5549561061548
2.3049114116164
47.676202304429

Time: 7.97 seconds, Memory: 4.00 MB

OK (10 tests, 11 assertions)
```

## ✍️ Authors

- [@azzedinedev](https://github.com/azzedinedev)
