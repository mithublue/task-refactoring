<?php
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{

    /**
     * @dataProvider transectionDataProvider
     */
    public function testReturnCommissions( $transectionData, $currency ) {

        //initiate CommissionCalculator instance
        $commissionCalculator = new \App\CommissionCalculator();

        //set transection data to the object
        $commissionCalculator->setTransectionData( $transectionData );

        /**
         * Provides settings to the instance
         */
        $commissionCalculator->setBinProvider( 'https://lookup.binlist.net' );
        $commissionCalculator->setCurrencyRatesProvider( 'https://api.exchangeratesapi.io/latest' );
        $commissionCalculator->setBaseCurrency( $currency );

        //get the commissions with ceiling
        $commissions = $commissionCalculator->returnCommissions( true );
        //

        if( is_array( $commissions ) ) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }


    public function transectionDataProvider() {
        return [
            'Test 1'  => [[
                '{"bin":"45717360","amount":"100.00","currency":"EUR"}',
                '{"bin":"516793","amount":"50.00","currency":"USD"}',
                '{"bin":"45417360","amount":"10000.00","currency":"JPY"}',
                '{"bin":"41417360","amount":"130.00","currency":"USD"}',
                '{"bin":"4745030","amount":"2000.00","currency":"GBP"}'
            ], 'EUR'],
            'Test 2' => [
                [
                    '{"bin":"65717835","amount":"100.00","currency":"EUR"}',
                    '{"bin":"71679380","amount":"50.00","currency":"USD"}',
                    '{"bin":"5413360","amount":"10000.00","currency":"JPY"}'
                ], 'USD'
            ]
        ];
    }
}