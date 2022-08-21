<?php

namespace App\Http\Controllers;

use CoinpaymentsAPI;
use Illuminate\Http\Request;
use \Exception;

class CoinController extends Controller
{
    public function getBalance(Request $request)
    {
        /** Scenario: Show balances of all coins in account with USD conversion.**/
// Create a new API wrapper instance and call to the balances and rates commands.

        $home_error = "";

        $coin_array = ['TRX', 'BTC', 'BCH', 'ETH', 'BNB', 'USDT', 'USDT.TRC20', 'LTCT'];
        $balance_error = false;
        $output = "";

        try {
            $cps_api = new CoinpaymentsAPI(env('COIN_PRIVATE_KEY'), env('COIN_PUBLIC_KEY'), 'json');
            $balances = $cps_api->GetAllCoinBalances();
            // dd($balances);
            $cps_api = null;
        } catch (Exception $e) {
            // echo 'Error: ' . $e->getMessage();
            // exit();
            $balance_error = true;
        }
        try {
            $cps_api = new CoinpaymentsAPI(env('COIN_PRIVATE_KEY'), env('COIN_PUBLIC_KEY'), 'json');
            $rates = $cps_api->GetShortRates();
        } catch (Exception $e) {
            // echo 'Error: ' . $e->getMessage();
            // exit();
            $balance_error = true;
        }

// Check for success of API calls
        if ($balances['error'] == 'ok' && $rates['error'] == 'ok') {

            // Prepare arrays for storing balances
            $positive_balances = [];
            $zero_balances = [];

            // Prepare start of sample HTML output
            $output = '<table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="bg-dark text-white">Coin</td>
                            <td class="bg-dark text-white">Satoshis Balance</td>
                            <td class="bg-dark text-white">Floating Point Balance</td>
                            <td class="bg-dark text-white">USD value</td>
                        </tr>';

            // Loop through balances and separate positive from zero balances
            foreach ($balances['result'] as $currency_balance => $balances_array) {
                if (in_array($currency_balance, $coin_array)) {
                    if ($balances_array['balance'] > 0) {
                        $positive_balances[$currency_balance] = $balances_array;
                    } else {
                        $zero_balances[$currency_balance] = $balances_array;
                    }
                }

            }

            // Check for positive balances and calculate the USD value for each
            if (!empty($positive_balances)) {
                $usd_to_btc = $rates['result']['USD']['rate_btc'];
                foreach ($positive_balances as $currency => $positive_balance) {
                    $this_currency_to_btc = $rates['result'][$currency]['rate_btc'];
                    $positive_balances[$currency]['usd_value'] = round($positive_balances[$currency]['balancef'] * ($this_currency_to_btc / $usd_to_btc), 2);
                }
            }

            // Loop through balances and add values to the output variable
            foreach ($positive_balances as $currency => $positive_balance) {
                $output .= '<tr>
                        <td>' . $currency . '</td>
                        <td>' . $positive_balance['balance'] . '</td>
                        <td>' . $positive_balance['balancef'] . '</td>
                        <td>' . $positive_balance['usd_value'] . '</td>
                    </tr>';
            }
            foreach ($zero_balances as $currency => $zero_balance) {
                $output .= '<tr>
                        <td>' . $currency . '</td>
                        <td>' . $zero_balance['balance'] . '</td>
                        <td>' . $zero_balance['balancef'] . '</td>
                        <td>0</td>
                    </tr>';
            }

            // Close the sample output HTML and echo it onto the page
            $output .= '</tbody></table>';

            return view('welcome', compact('output', 'home_error', 'balance_error', 'coin_array'));
        } else {

            // Throw an error if both API calls were not successful
            $home_error = 'Balances API call status: ' . $balances['error'] . '<br>Rates API call status: ' . $rates['error'];
            return view('welcome', compact('output', 'home_error', 'balance_error', 'coin_array'));
        }
    }

    public function postWithdraw(Request $request)
    {

        // 84mUMUnhcm3fogw1DqBuugY1sv8qLvST3Ss185dAgk6XDWQkN1WNCrDXQYa4dGKKtFKdwioMkHg2i6SFbfc7cpznExYZmAG

        // https://webhook.site/ac82cdb1-3d7e-4d3f-bc63-4078db15e048?

        /** Scenario: Create a mass withdrawal, demonstrating different values for each withdrawal. **/

        // Create a new API wrapper instance
        $cps_api = new CoinpaymentsAPI(env('COIN_PRIVATE_KEY'), env('COIN_PUBLIC_KEY'), 'json');

        // Setup the withdrawals array values, each as a nested array with it's own unique key.
        // The key can contain ONLY a-z, A-Z, and 0-9.
        // Withdrawals with empty keys or containing other characters will be silently ignored.

        $error = '';

        $withdrawals = [

            'wd1' => [
                'amount' => $request['amount'],
                'add_tx_fee' => 0,
                'currency' => $request['currency'],
                'address' => $request['address'],
                'ipn_url' => 'https://webhook.site/ac82cdb1-3d7e-4d3f-bc63-4078db15e048',
            ],
        ];

// Attempt the mass withdrawal API call
        try {
            $mass_withdrawal = $cps_api->CreateMassWithdrawal($withdrawals);
            // dd($mass_withdrawal);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return response()->json(['status' => 0, 'msg' => 'Error: ' . $e->getMessage()]);
            // exit();
        }

// Check the result of the API call and generate a result output
        if ($mass_withdrawal["error"] == "ok") {
            $output = '<table>
                        <tbody>
                            <tr>
                                <td>Withdrawal Key</td>
                                <td>Error?</td>
                                <td>ID</td>
                                <td>Status</td>
                                <td>Amount</td>
                            </tr>';
            foreach ($mass_withdrawal['result'] as $single_withdrawal_result => $single_withdrawal_result_array) {
                if ($single_withdrawal_result_array['error'] == 'ok') {
                    $this_id = $single_withdrawal_result_array['id'];
                    $this_status = $single_withdrawal_result_array['status'];
                    $this_amount = $single_withdrawal_result_array['amount'];
                    $output .= '<tr>
                                    <td>' . $single_withdrawal_result . '</td>
                                    <td>ok</td>
                                    <td>' . $this_id . '</td>
                                    <td>' . $this_status . '</td>
                                    <td>' . $this_amount . '</td></tr>';
                    return response()->json(['status' => 1, 'msg' => $this_amount . '  ' . $request['currency'] . '  Successfully Withdrawn']);
                    break;
                } else {
                    $this_error = $single_withdrawal_result_array['error'];
                    $output .= '<tr><td>' . $single_withdrawal_result . '</td><td>' . $this_error . '</td><td>n/a</td><td>n/a</td><td>n/a</td></tr>';
                    return response()->json(['status' => 0, 'msg' => 'Error: ' . $this_error]);
                }
            }
            $output .= '</tbody></table>';
            // echo $output;
        } else {
            return response()->json(['status' => 0, 'msg' => 'Error: ' . $mass_withdrawal["error"]]);

        }
    }
}
