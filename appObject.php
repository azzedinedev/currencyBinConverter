<?php
/* * * * * * * * * * * * * *********** * * * * * * * * * * * * * * * * * * * * * * * * *
 *            Calculate commissions for transactions based on credit card number in EURO
 * @author    SARIRETE AZZEDINE
 * @github :  github.com/azzedinedev
 * @linkedin: linkedin.com/in/azzdinedev
 * @mail      azzdinedev@gmail.com
 * * * * * * * * * * * * * * * * * * *********** * * * * * * * * * * * * * * * * * * * *
 */

class currency{
    
    var $amountResults;
    var $errors;
    var $APIBin;
    var $APIRates;

    function __construct()
    {
        $this->amountResults= array();
        $this->errors       = array();
        //You can update the API variables to change the provider of BIN provider
        $this->APIBin       = 'https://lookup.binlist.net/';
        //You can update the API variables to change the provider of exchange's rates
        $this->APIRates     = 'http://api.exchangeratesapi.io/latest?access_key=5a8c29cc1cfcf0ce1bc40485ccfb138a';
    }

    /**
     * Print the results of the manipulation on currencies
     * @param string $inputFile input file name
     */
    public function results($inputFile) {
        
        $jsonArray = $this->extractJsonFromFile($inputFile);
        //Check the input files are correct and ensure filename is in the correct 
        if( $jsonArray != false ){
            //At the start, the process is true
            $resultProcessing = true;
            foreach ($jsonArray as $json) {
                //Replace the special characers with blank to return the correct json format from the line
                $json   = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json);

                //Decode the string to a json format and store the json on the $obj array
                $obj    = json_decode($json, true);
                $bin        = $obj['bin'];
                $amount     = $obj['amount'];
                $currency   = $obj['currency'];
            
                //Verifiy the bin from binlist API
                $binDetail  = $this->binDetails($bin);
                if( $binDetail == false ){
                    //On error of the BIN provider set the processing to false
                    $resultProcessing = false;
                    break;
                }
                $isEu = $this->isEu($binDetail->country->alpha2);
                        
                //Extract amount of rates
                $amntFixed = $this->extractAmount($currency,$amount);
                if( $amntFixed == false ){
                    //On error of the exchange provider set the processing to false
                    $resultProcessing = false;
                    break;
                }

                //Calculate the amount result
                $amountResult = $amntFixed * ( ($isEu == true) ? 0.01 : 0.02);
                
                //Ceiling the amount withe 2 decimals
                $amountResult = $this->round_up($amountResult,2);

                //Store the amounts in a global array
                $this->amountResults[] = $amountResult;

                //Print the result on the screen
                echo $amountResult;
                print "\n";
            
            }
            if( !$resultProcessing ){
                $this->showErrros();
                return false;
            }

        }else{
            //You should specify a correct path of the file to be examined else we return false
            $this->showErrros();
            return false;
        }
    }

    /**
     * Check the input file and extact json lines as an array of json
     * @param string $inputFile input file name
     * @return array $jsonArray (array of the json lines)
     * @return bool If the format or the input file name is incorrect we return false
     */
    public function extractJsonFromFile($inputFile) {
        if( ($inputFile != null) and ($inputFile != "") and ( file_exists($inputFile) ) ){
            //Check the input files are correct and ensure filename is in the correct 
            
            //Expload the text from input file to an Array
            $jsonArray = explode("\n", @file_get_contents($inputFile));
            
            //Check the Json format and store in Array of json
            $errorJson = false;
            foreach ($jsonArray as $json) {
                //Replace the special characers with blank to return the correct json format from the line
                $json   = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json);

                //Decode the string to a json format and store the json on the $obj array
                $obj    = @json_decode($json, true);
                if( $obj == null ){
                    //if error on checking the json format return false and break the treatment
                    $errorJson = true;
                    break;
                }
            }    
            if( $errorJson == true ) {
                $this->errors[] = "Error : Checking the json format of the input file is failed";
                return false;
            }else{
                //return an array of valid json rows
                return $jsonArray;  
            }          
        }else{
            //Incorrect input file
            $this->errors[] = "Error : Incorrect input file";
            return false;
        }
    }

    /**
     * Verifiy the bin from binlist API
     * @param string $bin Bin code (the first numbers of the card)
     * @return array $r (Json object)
     */
    public function binDetails($bin) {
        $binResults = @file_get_contents($this->APIBin.$bin);    
        if (!$binResults) {
            $this->errors[] = "Error : Acces to BIN provider is impossible";
            return false;
        }else{
            $resultBin = @json_decode($binResults);
            if( $resultBin == null ){
                //Checking the corrcet JSON format is failed
                $this->errors[] = "Error : Checking the corrcet JSON format is failed from the BIN provider";
                return false;
            }else{
                return $resultBin;
            }
        }
    }

    /**
     * Test if the country is european
     * @param string $countryCode (Code of country on 2 uppercase letters)
     * @return string (yes or no)
     */
    public function isEu($countryCode) {
        $eurpeanCode = array('AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','FR','GR','HR','HU','IE','IT','LT','LU','LV','MT','NL','PO','PT','RO','SE','SI','SK');
        $result = (in_array($countryCode,$eurpeanCode))? true: false;
        return $result;
    }

    /**
     * Extract Amount from the APIexchangeratesapi.io
     * @param string $currency The cuurency on 3 uppercase letters
     * @param double $amount Ammount
     * @return double $amntFixed calculated amount
     */
    public function extractAmount($currency,$amount) {     
        if( $this->APIRates == "" ){
            $this->errors[] = "Error : The exchange provider must be configured"; 
            return false;
        }else{
            $rateContent = @file_get_contents($this->APIRates);
            if( !$rateContent ){
                $this->errors[] = "Error : The acces to exchange provider is impossible"; 
                return false;
            }else{
                $rate = @json_decode($rateContent, true)['rates'][$currency];
                if( !is_numeric($rate) ){
                    $this->errors[] = "Error : Problem on currencies";
                    return false;
                }else{
                    //The rate is extracted from the API then we calculate the amount
                    if ( ($currency == 'EUR') or ($rate == 0) ) {
                        $amntFixed = $amount;
                    }elseif ( ($currency != 'EUR') or ($rate > 0) ) {
                        $amntFixed = $amount / $rate;
                    }
                    return $amntFixed;    
                }  
            }
        }
    }

    /** 
     * Rounds up a float to a specified number of decimal places
     * (basically acts like ceil() but allows for decimal places)
     * @param float $value The number to be processedFunctions
     * @param interger Number of decimals
     */
    function round_up($value, $places=0) {
        if ($places < 0) { $places = 0; }
        $mult = pow(10, $places);
        return ceil($value * $mult) / $mult;
    }
        
    /**
     * Show the errors
     * @return Print the errors on screen
     */
    public function showErrros() {
        if( count($this->errors) > 0 ){
            echo 'Processing failed. the number of errors is: '.count($this->errors)."\n";
            foreach ($this->errors as $error) {
                echo $error."\n";
            }
        }
    }

}

?>