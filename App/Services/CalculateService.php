<?php
namespace App\Services;

use App\Infrastructure\sdbh;

class CalculateService
{
    private $dbh;

    public function __construct(sdbh $dbh)
    {
        $this->dbh = $dbh;
    }

    public function calculateProductPrice(int $productId, int $days, array $selectedServices): array
    {
        $rate = $this->getCurrencyRate();
        $product = $this->fetchProduct($productId);

        if (!$product) {
            throw new \Exception("Ошибка, товар не найден!");
        }

        $productPrice = $this->calculateTariff($product['PRICE'], $product['TARIFF'], $days);
        $servicesPrice = $this->calculateServicesPrice($selectedServices, $days);

        $totalPriceRUB = $productPrice + $servicesPrice;
        $totalPriceCNY = $totalPriceRUB / $rate; // Конвертируем в Юани

        return [
            'total_price_rub' => $totalPriceRUB,
            'total_price_cny' => $totalPriceCNY,
        ];
    }


    private function fetchProduct(int $productId): ?array
    {
        $product = $this->dbh->make_query("SELECT * FROM a25_products WHERE ID = $productId");
        return $product ? $product[0] : null;
    }

    private function calculateTariff(float $price, string $tariffSerialized, int $days): float
    {
        $tariffs = unserialize($tariffSerialized);
        $finalPrice = $price;

        if (is_array($tariffs)) {
            foreach ($tariffs as $dayCount => $tariffPrice) {
                if ($days >= $dayCount) {
                    $finalPrice = $tariffPrice;
                }
            }
        }

        return $finalPrice * $days;
    }

    private function calculateServicesPrice(array $selectedServices, int $days): float
    {
        $servicesPrice = 0;
        foreach ($selectedServices as $servicePrice) {
            $servicesPrice += (float)$servicePrice * $days;
        }
        return $servicesPrice;
    }

    public function getCurrencyRate(): float
    {
        $url = 'https://www.cbr-xml-daily.ru/daily_json.js';
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['Valute']['CNY']['Value'])) {
            return (float)$data['Valute']['CNY']['Value'];
        }

        throw new \Exception("Не удалось получить курс валют.");
    }
}

