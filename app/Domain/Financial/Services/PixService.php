<?php

namespace App\Domain\Financial\Services;

use Illuminate\Support\Str;

class PixService
{
    /**
     * Gera o Payload EMV (String Copia e Cola) para PIX.
     * 
     * @param string $pixKey Chave PIX do recebedor
     * @param string $merchantName Nome do recebedor
     * @param string $merchantCity Cidade do recebedor
     * @param float|null $amount Valor da cobrança
     * @param string $txid Identificador da transação
     * @return string
     */
    public function generatePayload(string $pixKey, string $merchantName, string $merchantCity, ?float $amount = null, string $txid = 'PGTO'): string
    {
        $pixKey = trim($pixKey);
        $merchantName = strtoupper(Str::ascii($merchantName));
        $merchantCity = strtoupper(Str::ascii($merchantCity));
        
        // O BCB exige que TXID seja apenas letras e números [a-zA-Z0-9]
        $txid = preg_replace('/[^a-zA-Z0-9]/', '', $txid);
        $txid = strtoupper($txid);
        
        // Limites do BCB
        $merchantName = substr($merchantName, 0, 25);
        $merchantCity = substr($merchantCity, 0, 15);
        $txid = substr($txid, 0, 25);

        if (empty($txid)) {
            $txid = '***';
        }

        $payload = "000201"; // Payload Format Indicator
        $payload .= $this->getGui($pixKey); // Merchant Account Information
        $payload .= "52040000"; // Merchant Category Code
        $payload .= "5303986"; // Transaction Currency (BRL)
        
        if ($amount !== null && $amount > 0) {
            $amountString = number_format($amount, 2, '.', '');
            $payload .= "54" . str_pad((string)strlen($amountString), 2, '0', STR_PAD_LEFT) . $amountString;
        }
        
        $payload .= "5802BR"; // Country Code
        $payload .= "59" . str_pad((string)strlen($merchantName), 2, '0', STR_PAD_LEFT) . $merchantName; // Merchant Name
        $payload .= "60" . str_pad((string)strlen($merchantCity), 2, '0', STR_PAD_LEFT) . $merchantCity; // Merchant City
        
        $txidField = "05" . str_pad((string)strlen($txid), 2, '0', STR_PAD_LEFT) . $txid;
        $payload .= "62" . str_pad((string)strlen($txidField), 2, '0', STR_PAD_LEFT) . $txidField; // Additional Data Field
        
        $payload .= "6304"; // CRC16 prefix
        $payload .= $this->crc16($payload);

        return $payload;
    }

    /**
     * Gera a string base64 do QR Code para ser usada em tags <img>
     */
    public function generateQrCodeBase64(string $payload): string
    {
        $options = new \chillerlan\QRCode\QROptions([
            'outputType' => 'svg',
            'imageBase64' => true,
            'scale' => 8,
            'imageTransparent' => false, // Evita fundo transparente pra contraste escuro
        ]);

        return (new \chillerlan\QRCode\QRCode($options))->render($payload);
    }

    private function getGui(string $pixKey): string
    {
        $guiString = "0014br.gov.bcb.pix01" . str_pad((string)strlen($pixKey), 2, '0', STR_PAD_LEFT) . $pixKey;
        return "26" . str_pad((string)strlen($guiString), 2, '0', STR_PAD_LEFT) . $guiString;
    }

    private function crc16(string $payload): string
    {
        $polynomial = 0x1021;
        $result = 0xFFFF;
        
        if (($length = strlen($payload)) > 0) {
            for ($offset = 0; $offset < $length; $offset++) {
                $result ^= (ord($payload[$offset]) << 8);
                for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                    if (($result <<= 1) & 0x10000) {
                        $result ^= $polynomial;
                    }
                    $result &= 0xFFFF;
                }
            }
        }
        return strtoupper(str_pad(dechex($result), 4, '0', STR_PAD_LEFT));
    }
}
