<?php

namespace App\Http\Controllers;

use App\Models\CustomerApplication;
use App\Models\PaymongoFormatter;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Ixudra\Curl\Facades\Curl;

class PaymongoController extends Controller
{
    public function pay(string $customerPaymentAccount, string $payment)
    {

        $l_payment = '';
        $l_customerPaymentAccount = '';
        parse_str($payment, $prsd_payment);
        parse_str($customerPaymentAccount, $prsd_customerPaymentAccount);
        // PaymongoFormatter::reverse_parse_str($payment, $l_payment);
        // PaymongoFormatter::reverse_parse_str($prsd_customerPaymentAccount, $l_customerPaymentAccount);



        $data = [
            'data' => [
                'attributes' => [
                    'line_items' => [
                        [
                            'currency'      => 'PHP',
                            'amount'        => PaymongoFormatter::convertNumber($prsd_payment['payment_amount']),
                            'name'          => 'Payment',
                            'quantity'      => 1,
                        ]
                    ],
                    'payment_method_types' => [
                        'gcash', 'card'
                    ],
                    'success_url' => route('payment-success', [
                        'customerPaymentAccount' => http_build_query($prsd_customerPaymentAccount),
                        'payment' => http_build_query($prsd_payment),
                    ]),
                    'cancel_url' => url('/customer/payments'),
                    'description' => "Payment Description",
                ],
            ]
        ];

        $response = Curl::to('https://api.paymongo.com/v1/checkout_sessions')
                    ->withHeader('Content-Type: application/json')
                    ->withHeader('accept: application/json')
                    ->withHeader('Authorization: Basic '.config('app.auth_pay'))
                    ->withData($data)
                    ->asJson()
                    ->post();

        // dd(env('AUTH_PAY'));
        // dd($response);
    
        Session::put('session_id',$response->data->id);

        return redirect()->to($response->data->attributes->checkout_url);
    }

    public function success(string $customerPaymentAccount, string $payment)
    {
        parse_str($payment, $payment);
        parse_str($customerPaymentAccount, $customerPaymentAccount);


        $sessionId = Session::get('session_id');
        $response = Curl::to('https://api.paymongo.com/v1/checkout_sessions/'.$sessionId)
                ->withHeader('accept: application/json')
                ->withHeader('Authorization: Basic '.config('app.auth_pay'))
                ->asJson()
                ->get();

        $amount = $response->data->attributes->line_items[0]->amount;

        Payment::query()->create([
            // 'payment_status' => $payment['payment_status'],
            // 'payment_type' => $payment['payment_type'],
            'customer_payment_account_id' => $customerPaymentAccount['id'],
            'term_covered' => $payment['term_covered'],
            'payment_is' => $payment['payment_is'],
            'rebate' => $payment['rebate'],
            'amount_to_be_paid' => $payment['amount_to_be_paid'],
            'payment_amount' => $payment['payment_amount'],
            'author_id' => auth()->user()->id,
            'branch_id' => $customerPaymentAccount['branch_id'],
        ]);

        return redirect(url('customer/customer-payment-accounts'));
    }

}