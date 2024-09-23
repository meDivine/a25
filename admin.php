<?php
require_once 'App/Domain/Users/UserEntity.php';
use App\Domain\Users\UserEntity;

$user = new UserEntity();
if (!$user->isAdmin)
    die('Доступ закрыт');
?>

<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
</head>
<body>
<div class="container">
    <h1>Добавить новый продукт</h1>
    <form id="add-product-form">
        <div class="mb-3">
            <label for="productName" class="form-label">Название продукта</label>
            <input type="text" class="form-control" id="productName" name="productName" required>
        </div>
        <div class="mb-3">
            <label for="productPrice" class="form-label">Цена продукта</label>
            <input type="number" class="form-control" id="productPrice" name="productPrice" required>
        </div>
        <div class="mb-3">
            <label for="productTariff" class="form-label">Тариф продукта</label>
            <input type="text" class="form-control" id="productTariff" name="productTariff" required>
        </div>
        <button type="submit" class="btn btn-primary">Добавить продукт</button>
    </form>
    <div id="response-message" class="mt-3"></div>
    <div id="validation-errors" class="text-danger mt-3"></div>
</div>

<script>
    $(document).ready(function() {
        $("#add-product-form").submit(function(event) {
            event.preventDefault();

            $("#validation-errors").text('');
            $("#response-message").text('');

            $.ajax({
                url: 'App/Application/AdminService.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $("#response-message").text(response.message).removeClass('text-danger').addClass('text-success');
                    } else {
                        $("#validation-errors").text(response.message);
                    }
                },
                error: function() {
                    $("#validation-errors").text('Ошибка при добавлении продукта.');
                }
            });
        });
    });
</script>
</body>
</html>
