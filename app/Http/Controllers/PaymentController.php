<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
   public function payment_first_method(Request $request){
      $merchantId = 6;
      $paymentId = 1;
      $limit = 100;
      $fields = [
         "merchant_id" => $merchantId,
         "payment_id" => $paymentId,
         "status" => "new",
         "amount" => $request->amount,
         "amount_paid" => $request->amount,
         "timestamp" => Carbon::now()->timestamp,
      ];
      $replaceSymbol = ":";
      $key = 'KaTf5tZYHx4v7pgZ';
      $payment = new Payment($fields, $key, $replaceSymbol);

      return $payment->send_json_payment(true, "https://example.com", $limit);
   }

   public function payment_second_method(Request $request){
      $merchantId = 816;
      $paymentId = 2;
      $limit = 100;
      $fields = [
         "project" => $merchantId,
         "invoice" => $paymentId,
         "status" => "new",
         "amount" => $request->amount,
         "amount_paid" => $request->amount,
         "rand" => Str::random()
      ];
      $replaceSymbol = ".";
      $key = 'rTaasVHeteGbhwBx';
      $payment = new Payment($fields, $key, $replaceSymbol);

      return $payment->send_form_payment("https://example.ru", $limit);
   }
}
