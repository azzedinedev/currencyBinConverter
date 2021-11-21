<?php
require "appObject.php";
use PHPUnit\Framework\TestCase as TestCase;

class currencyTest extends TestCase
{
    
    /**
     * Check if the file exist or not
     * the file "input.txt" exist
     */
    public function testFileExist(){
        $currency = new currency();
        $this->assertFalse($currency->results(""));
        $this->assertFileExists("input.txt");
    }

    /**
     * extractJsonFromFile return an array if the input file exist and his json format is checked as true
     */
    public function testInputAsArray(){
        $currency = new currency();
        $this->assertIsArray($currency->extractJsonFromFile("input.txt"));
    }

    /**
     * A bad format on existing file input it is not an array (return false)
     */
    public function testInputIsNotArray(){
        $currency = new currency();
        $this->assertIsNotArray($currency->extractJsonFromFile("inputBadJson.txt"));
    }

    /**
     * A bad format on existing file input it is not an array (return false)
     */
    public function testBadJsonInput(){
        $currency = new currency();
        $this->assertEquals(null,$currency->extractJsonFromFile("inputBadJson.txt"));
    }
    
    /**
     * Count of array is 5 (file exist on good format)
     */
    public function testArrayCount(){
        $currency = new currency();
        $this->assertCount(5, $currency->extractJsonFromFile("input.txt"));
    } 

    /**
     * Check the object of the bin extracted from the API
     * the results must be an object
     */
    public function testBinDetails(){
        $currency = new currency();
        $this->assertIsObject($currency->binDetails("457173"));
    }

    /**
     * Check country code as european
     */
    public function testIsEuropean(){
        $currency = new currency();
        $this->assertEquals(true,$currency->isEu("AT"));
    }
    
    /**
     * Check country code as not european
     */
    public function testIsNotEuropean(){
        $currency = new currency();
        $this->assertEquals(false,$currency->isEu("DZ"));
    }
    
    /**
     * Check the amount
     */
    public function testExtractAmount(){
        $currency = new currency();
        $this->assertEquals(1,$currency->extractAmount('EUR',1));
    }

    /**
     * Check the final result
     * the results are stored on an array being 5 elements
     */
    public function testArrayOfResult(){
        $currency = new currency();
        $currency->results("input.txt");
        $resultArray  = $currency->amountResults;
        
        $this->assertCount(5,$resultArray);
    }

     /**
     * Check the errors on BIN API
     * the results must return false and the array of errors is not empty
     */
    public function testOnErrorAPIBin(){
        $currency = new currency();
        $currency->APIBin = "";
        $result = $currency->results("input.txt");
        
        $this->assertFalse($result);
        $this->assertNotEmpty($currency->errors);
    }

     /**
     * Check the errors on Rates API
     * the results must return false and the array of errors is not empty
     */
    public function testOnErrorAPIRates(){
        $currency = new currency();
        $currency->APIRates = "";
        $result = $currency->results("input.txt");
        
        $this->assertFalse($result);
        $this->assertNotEmpty($currency->errors);
    }

}
?>