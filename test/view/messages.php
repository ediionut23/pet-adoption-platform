<?php
require_once '../utils/auth_middleware.php';
$user = checkAuth();

session_start();
require_once '../controllers/MessageController.php';


function safeEcho($value, $default = '') {
    return htmlspecialchars($value ?? $default);
}

$controller = new MessageController();

$petId = $_GET['pet_id'] ?? null;
$ownerId = $_GET['owner_id'] ?? null;

if ($petId && $ownerId) {
    $result = $controller->startConversation($petId);
    if (isset($result['error'])) {
        echo "Error: " . $result['error'];
        exit;
    }
    $pet = $result['pet'];
}

$conversationsResult = $controller->getConversations();
$conversations = $conversationsResult['conversations'] ?? [];

$selectedConversationId = $_GET['conversation_id'] ?? null;
$messages = [];
if ($selectedConversationId) {
    $messagesResult = $controller->getMessages($selectedConversationId);
    $messages = $messagesResult['messages'] ?? [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Pow</title>
    <link rel="stylesheet" href="../stiluri/messages.css">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="lista-animale.php" class="back-btn">Back</a>

        <a href="homepage.php" class="logo">Pow</a>

        <?php if ($ownerId == $user->id): ?>
            <a href="profile.php" class="profile-icon">
                <img src="../stiluri/imagini/profileicon.png" alt="Profile" />
            </a>
        <?php else: ?>
            <a href="formular.php?pet_id=<?php echo $petId; ?>" class="adopt-nav-btn">Adopt</a>
        <?php endif; ?>
    </header>

    <div class="messages-container">
        <div class="conversations-list">
            <h2>Conversations</h2>
            <?php if (empty($conversations) && !$petId): ?>
                <p class="no-conversations">No conversations yet.</p>
            <?php else: ?>
                <?php foreach ($conversations as $conv): ?>
                    <a href="?conversation_id=<?php echo $conv['CONVERSATION_ID']; ?>&pet_id=<?php echo $conv['PET_ID']; ?>&owner_id=<?php echo $conv['OTHER_USER_ID']; ?>" 
                       class="conversation-item <?php echo $selectedConversationId == $conv['CONVERSATION_ID'] ? 'active' : ''; ?>">
                        <div class="conversation-info">
                            <div class="user-name">
                                <?php echo safeEcho(($conv['OTHER_USER_NAME'] ?? '') . ' ' . ($conv['OTHER_USER_SURNAME'] ?? '')); ?>
                            </div>
                            <div class="pet-name">
                                About: <?php echo safeEcho($conv['PET_NAME'] ?? ''); ?>
                            </div>
                            <div class="last-message">
                                <?php 
                                    $lastMessage = $conv['LAST_MESSAGE'] ?? '';
                                    echo safeEcho(substr($lastMessage, 0, 50)) . (strlen($lastMessage) > 50 ? '...' : ''); 
                                ?>
                            </div>
                        </div>
                        <?php if ($conv['UNREAD_COUNT'] > 0): ?>
                            <div class="unread-badge"><?php echo $conv['UNREAD_COUNT']; ?></div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="chat-container">
            <?php if ($selectedConversationId || ($petId && $ownerId)): ?>
                <div class="chat-messages" id="chat-messages">
                    <?php if (!empty($messages)): ?>                        <?php foreach ($messages as $message): 
                            if (isset($message['MESSAGE_TEXT']) && trim($message['MESSAGE_TEXT']) !== ''): ?>
                            <div class="message <?php echo $message['SENDER_ID'] == $user->id ? 'sent' : 'received'; ?>">
                                <div class="message-content">
                                    <?php echo nl2br(safeEcho($message['MESSAGE_TEXT'])); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php elseif ($petId && $ownerId): ?>
                        <div class="start-conversation">
                            <p>Start a conversation about <?php echo !empty($pet['NAME']) ? safeEcho($pet['NAME']) : 'this pet'; ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <form class="message-form" id="message-form" method="POST">                    <input type="hidden" name="receiver_id" value="<?php 
                        if ($ownerId) {
                            echo $ownerId;
                        } elseif (!empty($messages) && isset($messages[0]['SENDER_ID'], $messages[0]['RECEIVER_ID'])) {
                            echo $messages[0]['SENDER_ID'] == $user->id ? $messages[0]['RECEIVER_ID'] : $messages[0]['SENDER_ID'];
                        }
                    ?>">
                    <input type="hidden" name="pet_id" value="<?php 
                        if ($petId) {
                            echo $petId;
                        } elseif (!empty($messages) && isset($messages[0]['PET_ID'])) {
                            echo $messages[0]['PET_ID'];
                        }
                    ?>">
                    <textarea name="message" placeholder="Type your message..." required></textarea>
                    <button type="submit">Send</button>
                </form>
            <?php else: ?>
                <div class="no-chat-selected">
                    <p>Select a conversation or start a new one from a pet's page.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function scrollToBottom() {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        scrollToBottom();

        document.getElementById('message-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('../utils/ajax_send_message.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    form.querySelector('textarea').value = '';
                    
                    window.location.reload();
                } else {
                    alert(result.error || 'Failed to send message');
                }
            } catch (error) {
                alert('Failed to send message');
            }
        });
    </script>
</body>
</html>