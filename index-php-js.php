<?php
// Обработка отправки сообщения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['username'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $message = htmlspecialchars(trim($_POST['message']));
    $time = date('H:i:s');
    if (!empty($username) && !empty($message)) {
        $logEntry = "[$time] $username: $message\n";
        file_put_contents('chat.log', $logEntry, FILE_APPEND);
    }   
    // Перенаправление для предотвращения повторной отправки при обновлении
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
// Чтение сообщений из файла
$messages = [];
if (file_exists('chat.log')) {
    $messages = array_reverse(file('chat.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Простой чат на PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .chat-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .chat-header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 1.2em;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        .message-time {
            font-size: 0.8em;
            color: #777;
        }
        .message-username {
            font-weight: bold;
            color: #4CAF50;
        }
        .chat-form {
            padding: 15px;
            background-color: #f9f9f9;
        }
        .form-group {
            margin-bottom: 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 80px;
            resize: vertical;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        button:hover {
            background-color: #45a049;
        }
        .meta-info {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            Простой чат на PHP и JS
        </div>
        <div class="chat-messages" id="messages-container">
            <?php if (empty($messages)): ?>
                <div class="message">Чат пуст. Будьте первым, кто оставит сообщение!</div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message">
                        <div class="message-time"><?php echo substr($msg, 1, 8); ?></div>
                        <div class="message-username"><?php 
                            $parts = explode(':', $msg, 3);
                            echo trim($parts[1]);
                        ?></div>
                        <div><?php 
                            $parts = explode(':', $msg, 3);
                            echo isset($parts[2]) ? trim($parts[2]) : '';
                        ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <form class="chat-form" method="post" action="">
            <div class="form-group">
                <label for="username">Ваше имя:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="message">Сообщение:</label>
                <textarea id="message" name="message" required></textarea>
            </div>  
            <button type="submit">Отправить</button>
        </form>
    </div>
    <div class="meta-info">
        Чат теперь автоматически обновляет сообщения каждые 3 секунды.
    </div>
    <script>
function refreshMessages() {
    // 1. Получаем ссылку на контейнер с сообщениями в DOM
    const container = document.getElementById('messages-container');   
    // 2. Отправляем GET-запрос на текущий URL страницы
    fetch(window.location.href)
        // 3. Когда получаем ответ, преобразуем его в текст
        .then(response => response.text())
        // 4. Работаем с полученным HTML
        .then(html => {
            // 5. Создаем парсер для обработки HTML
            const parser = new DOMParser();
            // 6. Парсим полученный HTML в DOM-структуру
            const doc = parser.parseFromString(html, 'text/html');
            // 7. Ищем в новом DOM контейнер с сообщениями
            const newContainer = doc.getElementById('messages-container');
            
            // 8. Если контейнер найден
            if (newContainer) {
                // 9. Заменяем содержимое текущего контейнера новым содержимым
                container.innerHTML = newContainer.innerHTML;
            }
        })
        // 10. Обрабатываем возможные ошибки
        .catch(error => console.error('Ошибка при обновлении сообщений:', error));
}
// 11. Устанавливаем интервал: вызываем refreshMessages каждые 3000 мс (3 секунды)
setInterval(refreshMessages, 3000);
    </script>
</body>
</html>
