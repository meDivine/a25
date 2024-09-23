<?php
namespace App\Application;

require_once '../Infrastructure/sdbh.php';
require_once '../Domain/Users/UserEntity.php';
use App\Infrastructure\sdbh;
use App\Domain\Users\UserEntity;

class AdminService {

    /** @var UserEntity */
    public $user;
    private $dbh;

    public function __construct()
    {
        $this->user = new UserEntity();
        $this->dbh = new sdbh();
    }

    public function addNewProduct()
    {
        if (!$this->user->isAdmin) {
            return ['status' => 'error', 'message' => 'Недостаточно прав.'];
        }
        $productName = isset($_POST['productName']) ? trim($_POST['productName']) : null;
        $productPrice = isset($_POST['productPrice']) ? trim($_POST['productPrice']) : null;
        $productTariff = isset($_POST['productTariff']) ? trim($_POST['productTariff']) : null;

        // Валидация
        if (empty($productName) || !is_string($productName)) {
            return ['status' => 'error', 'message' => 'Название продукта должно быть текстом.'];
        }
        if (empty($productTariff) || !is_string($productTariff)) {
            return ['status' => 'error', 'message' => 'Тариф продукта должен быть текстом.'];
        }
        if (!is_numeric($productPrice)) {
            return ['status' => 'error', 'message' => 'Цена продукта должна быть числом.'];
        }

        // Добавление продукта в бд
        $query = "INSERT INTO a25_products (NAME, PRICE, TARIFF) VALUES ('$productName', $productPrice, '$productTariff')";

        if ($this->dbh->query_ds_exc($query) === TRUE) {
            return ['status' => 'success', 'message' => 'Продукт успешно добавлен.'];
        } else {
            return ['status' => 'error', 'message' => 'Ошибка при добавлении продукта: ' . $this->dbh->query_ds_exc->error];
        }

        return ['status' => 'error', 'message' => 'Не все поля заполнены.'];
    }

}

// Обработка AJAX-запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminService = new AdminService();
    $response = $adminService->addNewProduct();
    echo json_encode($response);
}
