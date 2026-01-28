<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешён']);
    exit;
}

// Получаем и очищаем данные
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$service = isset($_POST['service']) ? trim(strip_tags($_POST['service'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

// Валидация (требуем имя и телефон)
if (strlen($name) < 2 || strlen($phone) < 10) {
    http_response_code(400);
    echo json_encode(['error' => 'Имя и телефон обязательны для заполнения']);
    exit;
}

// Кому отправлять
$to = 'poskultura@mail.ru';
$subject = 'Новая заявка с сайта POSkультура';

// Заголовки письма
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=utf-8\r\n";
$headers .= "From: noreply@poskultura.ru\r\n";
if ($email) {
    $headers .= "Reply-To: " . $email . "\r\n";
}

// Тело письма
$body = "
<html>
<body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
<h2 style='color: #2563eb;'>Новая заявка с сайта POSkультура</h2>
<p><strong>Имя:</strong> " . htmlspecialchars($name) . "</p>
<p><strong>Телефон:</strong> " . htmlspecialchars($phone) . "</p>
<p><strong>Email:</strong> " . ($email ? htmlspecialchars($email) : '—') . "</p>
<p><strong>Услуга:</strong> " . ($service && $service !== 'Выберите услугу' ? htmlspecialchars($service) : '—') . "</p>
<p><strong>Сообщение:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
<hr>
<p><small>Отправлено автоматически с poskultura.ru</small></p>
</body>
</html>
";

// Отправляем
if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Не удалось отправить письмо. Попробуйте позже.']);
}