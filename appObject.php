<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   HOME WORK SOLUTION
 * @author    SARIRETE AZZEDINE
 * @github :  github.com/azzedinedev
 * @linkedin: linkedin.com/in/azzdinedev
 * @mail      azzdinedev@gmail.com
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

class currency{
    
    var $amountResults;
    
    function __construct()
    {
        $this->amountResults = array();
    }


    /**
     * Print the results of the manipulation on currencies
     * @param string $inputFile input file name
     */
    public function results($inputFile) {
        
        $jsonArray = $this->extractJsonFromFile($inputFile);
        //Check the input files are correct and ensure filename is in the correct 
        if( $jsonArray != false ){

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
                $isEu = $this->isEu($binDetail->country->alpha2);
                        
                //Update the acces key for http://api.exchangeratesapi.io/v1/latest?access_key=5a8c29cc1cfcf0ce1bc40485ccfb138a
                $amntFixed = $this->extractAmount($currency,$amount);

                //Calculate the amount result
                $amountResult = $amntFixed * ($isEu == 'yes' ? 0.01 : 0.02);

                //Store the amounts in a global array
                $this->amountResults[] = $amountResult;

                //Print the result on the screen
                echo $amountResult;
                print "\n";
            
            }
        }else{
            //You should specify a correct path of the file to be examined else we return false
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
            $jsonArray = explode("\n", file_get_contents($inputFile));
            
            //Check the Json format and store in Array of json
            $errorJson = false;
            foreach ($jsonArray as $json) {
                //Replace the special characers with blank to return the correct json format from the line
                $json   = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json);

                //Decode the string to a json format and store the json on the $obj array
                $obj    = json_decode($json, true);
                if( $obj == null ){
                    //if error on checking the json format return false and break the treatment
                    $errorJson = true;
                    break;
                }
            }    
            if( $errorJson == true ) {
                return false;
            }else{
                return $jsonArray;  
            }
            
        }else{
            //Incorrect input file
            return false;
        }
    }

    /**
     * Verifiy the bin from binlist API
     * @param string $bin Bin code (the first numbers of the card)
     * @return array $r (Json object)
     */
    public function binDetails($bin) {
        $binResults = file_get_contents('https://lookup.binlist.net/' .$bin);    
        if (!$binResults)
            die('error!');
        $r = json_decode($binResults);
        return $r;
    }

    /**
     * Test if the country is european
     * @param string $countryCode (Code of country on 2 uppercase letters)
     * @return string (yes or no)
     */
    public function isEu($countryCode) {
        $eurpeanCode = array('AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','FR','GR','HR','HU','IE','IT','LT','LU','LV','MT','NL','PO','PT','RO','SE','SI','SK');
        $result = (in_array($countryCode,$eurpeanCode))? 'yes': 'no';
        return $result;
    }

    /**
     * Extract Amount from the APIexchangeratesapi.io
     * @param string $currency The cuurency on 3 uppercase letters
     * @param double $amount Ammount
     * @return double $amntFixed calculated amount
     */
    public function extractAmount($currency,$amount) {        
        $rate = json_decode(file_get_contents('http://api.exchangeratesapi.io/latest?access_key=5a8c29cc1cfcf0ce1bc40485ccfb138a'), true)['rates'][$currency];
    
        if ($currency == 'EUR' or $rate == 0) {
            $amntFixed = $amount;
        }elseif ($currency != 'EUR' or $rate > 0) {
            $amntFixed = $amount / $rate;
        }
        return $amntFixed;
    }

}

?>