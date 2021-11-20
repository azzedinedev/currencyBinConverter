<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   HOME WORK SOLUTION
 * @author    SARIRETE AZZEDINE
 * @github :  github.com/azzedinedev
 * @linkedin: linkedin.com/in/azzdinedev
 * @mail      azzdinedev@gmail.com
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */
require 'appObject.php';

if( count($argv) > 1 ){
    $currency = new currency();
    $currency->results($argv[1]);
}else{
    echo "You must define an input file\n";
}


?>