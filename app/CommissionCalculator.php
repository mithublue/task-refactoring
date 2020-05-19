<?php

namespace App;
use PHPUnit\Exception;

/**
 * This class is to calculate the commissions
 *
 * Class Commission_Calculator
 */
class CommissionCalculator {


    /**
     * Holds the transections data
     *
     * @var array
     */
    protected $transactions = [];
    protected $currencyRateProvider;
    protected $binProvider;
    protected $baseCurrency = 'EUR';

    /**
     * @param array $transectionData
     */
    public function setTransectionData( $transectionData = [] ) {
        $this->transactions = $transectionData;
    }

    /**
     * Return transection data
     *
     * @return array
     */
    public function getTransectionData() {
        return $this->transactions;
    }

    /**
     * @param null $url
     */
    public function setCurrencyRatesProvider( $url = null ) {
        if( $url ) {
            $this->currencyRateProvider = $url;
        }
    }

    /**
     * @param null $url
     */
    public function setBinProvider( $url = null ) {
        $this->binProvider = $url;
    }

    /**
     * Return url of currency rates provider
     *
     * @return mixed
     */
    public function getCurrencyRatesProvider() {
        return $this->currencyRateProvider;
    }

    /**
     * Return url of bin provider
     * @return mixed
     */
    public function getBinProvider() {
        return $this->binProvider;
    }

    /**
     * Sets base currency
     *
     * @param $baseCurrency
     */
    public function setBaseCurrency( $baseCurrency ) {
        $this->baseCurrency = $baseCurrency;
    }

    /**
     * Returns base currency
     *
     * @return mixed
     */
    public function getBaseCurrency() {
        return $this->baseCurrency;
    }


    /**
     * Calculate the commissions
     * of each transections and
     * output the results
     */
    public function returnCommissions( $ceil = false ) {

        $commissions = [];

        foreach ( $this->transactions as $row) {
            if ( empty( $row ) ) continue;

            $transectionRow = json_decode( $row,true );
            if( !$transectionRow ) continue;

            $binResults = $this->getBinResponse( $transectionRow['bin'] );
            if( !$binResults ) {
                $commissions[] = null;
                continue;
            }

            $binResults = json_decode($binResults);
            $isEu = $this->isEu( $binResults->country->alpha2 );
            $rate = $this->getRate( $transectionRow['currency'] );

            if ( $transectionRow['currency'] == $this->getBaseCurrency() || !$rate ) {
                $amntFixed = $transectionRow['amount'];
            } else {
                $amntFixed = $transectionRow['amount'] / $rate;
            }

            $amntFixed = $amntFixed * ( $isEu ? 0.01 : 0.02 );
            $commissions[] = $ceil ? round( $amntFixed, 2 ) : $amntFixed;
        }

        return $commissions;
    }

    /**
     * The  function is check if the currency
     * is from EU ro not
     *
     * @param $currency
     * @return bool
     */
    function isEu( $currency ) {

        switch( $currency ) {
            case 'AT':
            case 'BE':
            case 'BG':
            case 'CY':
            case 'CZ':
            case 'DE':
            case 'DK':
            case 'EE':
            case 'ES':
            case 'FI':
            case 'FR':
            case 'GR':
            case 'HR':
            case 'HU':
            case 'IE':
            case 'IT':
            case 'LT':
            case 'LU':
            case 'LV':
            case 'MT':
            case 'NL':
            case 'PO':
            case 'PT':
            case 'RO':
            case 'SE':
            case 'SI':
            case 'SK':
                $result = true;
                break;
            default:
                $result = false;
        }

        return $result;
    }


    /**
     * Get response from bin url
     *
     * @param $bin
     * @return bool|mixed
     */
    protected function getBinResponse( $bin ) {

        $url = $this->getBinProvider(). '/' .$bin;
        $response = $this->fetch($url);
        return $response;
    }


    /**
     * Return the rate of inputted currency
     * @param $currency
     * @return mixed
     */
    protected function getRate( $currency ) {
        $rate = null;
        $url = $this->getCurrencyRatesProvider().'?base='.$this->getBaseCurrency();
        $response = $this->fetch($url);
        $response = json_decode( $response, true );

        if( $response ) {
            $rate = isset( $response['rates'][$currency] ) ? $response['rates'][$currency] : null;
        }

        return $rate;
    }


    /**
     * Fetching  data from the url
     *
     * @param $url
     * @return bool|mixed
     */
    public function fetch( $url ) {
        $handle = curl_init($url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        //check status code
        if( $httpCode !== 200 ) {
            $response = false;
        }

        curl_close($handle);

        return $response;
    }
}