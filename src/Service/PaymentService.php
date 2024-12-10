<?php

namespace App\Service;

use App\Entity\Payment;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentService
{
    public function __construct(
        private readonly HttpClientInterface $client,
    ) {
    }

    private string $baseUrlFlexPay = 'https://backend.flexpay.cd/api/rest/v1/';
    private string $token = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJcL2xvZ2luIiwicm9sZXMiOlsiTUVSQ0hBTlQiXSwiZXhwIjoxNzkxMTExNDk0LCJzdWIiOiI3NjE1N2IzNDU0MzRkYTAxOGJiYzQzM2QwN2NiOTE0YSJ9.LbzEEJKQf8h3aLDF1H2IR5dxocN8jQKShX4i711kbFo';

    /**
     * @throws \JsonException
     */
    public function makePayment(Payment $payment): array
    {
        $data = [
            "merchant"      => "INNOVATION_TECH",
            "type"          => "1",
            "phone"         => $payment->getPhoneNumber(),
            "reference"     => $payment->getRef(),
            "amount"        => $payment->getAmount(),
            "currency"      => $payment->getCurrency()->getCode(),
            "callbackUrl"   => "https://abc.abcd",
        ];

        $data = json_encode($data, JSON_THROW_ON_ERROR);
        $gateway = $this->baseUrlFlexPay . "paymentService";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gateway);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: " . $this->token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        $response = curl_exec($ch);

        $orderNumber = "";
        if (curl_errno($ch)) {
            $message = 'Une erreur lors du traitement de votre requête';
            $success = false;
        } else {
            curl_close($ch);
            $jsonRes = json_decode($response, false, 512, JSON_THROW_ON_ERROR);
            $code = $jsonRes->code;
            $message = $jsonRes->message ?? 'Impossible de traiter la demande, veuillez réessayer';
            if ($code !== "0") {
                $success = false;
            } else {
                $success = true;
                $orderNumber = $jsonRes->orderNumber;
            }
        }

        return [
            'success' => $success,
            'orderNumber' => $orderNumber,
            'message' => $message
        ];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function checkPaymentStatus(Payment $payment): array
    {
        $response = $this->client->request(
            'GET',
            $this->baseUrlFlexPay . 'check/' . $payment->getOrderNumber(),
            [
                'headers' => [
                    'Accept: */*',
                    'Authorization: ' . $this->token,
                ],
            ]
        );
        $message = '';
        $success = false;

        $content = $response->toArray();
        if ($content["transaction"]) {
            if ($content["transaction"]["status"] === "0") {
                $message = 'Paiement éffectué avec success';
                $success = true;
            } else {
                $message = $content["message"];
            }
        }
        return [
            'success' => $success,
            'waiting' => $content["transaction"]["status"] == "2",
            'message' => $message
        ];
    }

    public function formatPhoneNumber (string $phoneNumber): ?string {
        if (str_starts_with($phoneNumber, '243') and strlen($phoneNumber) == 12) {
            return $phoneNumber;
        }

        if (str_starts_with($phoneNumber, '0') and strlen($phoneNumber) == 10) {
            return '243' . mb_substr($phoneNumber, 1);
        }

        if (strlen($phoneNumber) === 9 && (str_starts_with($phoneNumber, '9') or str_starts_with($phoneNumber, '8'))) {
            return '243' . $phoneNumber;
        }

        return null;
    }
}