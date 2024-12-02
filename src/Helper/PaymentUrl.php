<?php

namespace App\Helper;

use App\Entity\Payment;


class PaymentUrl{
    public function paymentUrl(Payment $payment, string $host): string{
        
        $postData = ["PayType"=>"MaxiCash","Amount"=>$payment->getAmount() * 100,"Currency"=>$payment->getCurrency(),
        "Telephone"=>$payment->getPhone(),"Email"=>$payment->getEmail(),"MerchantID"=>"2bd7fd5caedc48dd8c5bcabee629812b","MerchantPassword"=>"55a6046137584680abddafe262985ff2",
        "Language"=>"fr","Reference"=>$payment->getReference(),"Accepturl"=>$host."/vote/process/success",
        "Cancelurl"=>$host."/vote/process/cancel","Declineurl"=>$host."/vote/process/fail",
        "NotifyURL"=>$host."/vote/process/notify"];
        
        $jsonData = json_encode($postData);
        $maxiUrl = 'https://api.maxicashapp.com/payentry?data='.$jsonData;
        return $maxiUrl;
       
    }
}