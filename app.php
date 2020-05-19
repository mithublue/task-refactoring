<?php

require_once __DIR__ . '/app/CommissionCalculator.php';

$transections = explode("\n", file_get_contents( './data/input.txt' ) );

if( is_array( $transections ) ) {

    //initiate CommissionCalculator instance
    $commissionCalculator = new \App\CommissionCalculator();

    //set transection data to the object
    $commissionCalculator->setTransectionData( $transections );

    /**
     * Provides settings to the instance
     */
    $commissionCalculator->setBinProvider( 'https://lookup.binlist.net' );
    $commissionCalculator->setCurrencyRatesProvider( 'https://api.exchangeratesapi.io/latest' );
    $commissionCalculator->setBaseCurrency( 'EUR' );

    //get the commissions without ceiling
    $commissions = $commissionCalculator->returnCommissions();

    //Output
    foreach ( $commissions as $commission ) {
        echo $commission;
        echo '<br>';
    }
}