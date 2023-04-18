<?php

namespace App\Http\Controllers;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller {
    //

    public function PaymentView() {
        return view( 'payment' );
    }

    public function payment( Request $request ) {

        $provider = new PayPalClient;

        $provider->setApiCredentials( config( 'paypal' ) );

        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder( [
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route( 'paypal.success' ),
                'cancel_url' => route( 'paypal.cancel' ),
            ],
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $request->amount
                    ]
                ]
            ]
        ] );

        // dd( $response );

        if ( isset( $response[ 'id' ] ) && $response[ 'id' ] != null ) {

            foreach ( $response[ 'links' ] as $link ) {

                if ( $link[ 'rel' ] === 'approve' ) {
                    return redirect()->away( $link[ 'href' ] );
                }
            }
        } else {
            return redirect()->route( 'paypal.cancel' );
        }

    }

    public function success( Request $request ) {
        $provider = new PayPalClient;

        $provider->setApiCredentials( config( 'paypal' ) );

        $paypalToken = $provider->getAccessToken();

        $response = $provider->capturePaymentOrder( $request->token );

        // dd( $response );

        if ( isset( $response[ 'status' ] ) && $response[ 'status' ] == 'COMPLETED' ) {
            $payment_id = $response[ 'purchase_units' ][ 0 ][ 'payments' ][ 'captures' ][ 0 ][ 'id' ];
            $amount = $response[ 'purchase_units' ][ 0 ][ 'payments' ][ 'captures' ][ 0 ][ 'amount' ][ 'value' ];
            $currency = $response[ 'purchase_units' ][ 0 ][ 'payments' ][ 'captures' ][ 0 ][ 'amount' ][ 'currency_code' ];
            $created_at = $response[ 'purchase_units' ][ 0 ][ 'payments' ][ 'captures' ][ 0 ][ 'create_time' ];
            $status = $response[ 'status' ];
            // dd( [ $payment_id, $amount, $currency, $created_at, $status ] );
            Transaction::create( [
                'payment_id'=>$payment_id,
                'amount'=>$amount,
                'currency'=>$currency,
                'created_at'=>$created_at,
                'status'=>$status
            ] );
            return redirect()->route( 'transaction.index' )->with( 'message', 'Paid Successfully! and Details stored in DB' );
        }

        return redirect()->route( 'paypal.cancel' );

    }

    public function cancel() {
        return 'Paymnet faild';

    }

    public function index( Request $request ) {
        $Transactions = Transaction::select( 'id', 'payment_id', 'amount', 'currency', 'status', 'created_at' )->get();
        return view( 'index', compact( 'Transactions' ) );
        $students = Student::select( 'first_name', 'last_name' );
        return Datatables::of( $students )->make( true );

    }

    function postdata( Request $request ) {
        $validation = Validator::make( $request->all(), [
            'payment_id' => 'required',
            'amount'  => 'required',
            'currency'  => 'required',
            'status'  => 'required',
            'created_at'  => 'required',

        ] );

        $error_array = array();
        $success_output = '';
        if ( $validation->fails() ) {
            foreach ( $validation->messages()->getMessages() as $field_name => $messages ) {
                $error_array[] = $messages;
            }
        } else {
            if ( $request->get( 'button_action' ) == 'insert' ) {
                $student = new Transaction( [
                    'payment_id'    =>  $request->get( 'payment_id' ),
                    'amount'     =>  $request->get( 'amount' ),
                    'currency'     =>  $request->get( 'currency' ),
                    'status'     =>  $request->get( 'status' ),
                    'created_at'     =>  $request->get( 'created_at' ),

                ] );
                $student->save();
                $success_output = '<div class="alert alert-success">Data Inserted</div>';
            }
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode( $output );
    }
}
