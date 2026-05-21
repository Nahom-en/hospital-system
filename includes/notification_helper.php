<?php
function send_notification($pdo, $user_id, $title, $message) {
    try {
        $stmt = $pdo->prepare("INSERT INTO notification (user_id, title, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $title, $message]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function get_unread_notifications_count($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notification WHERE user_id = ? AND is_read = FALSE");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function get_notifications($pdo, $user_id, $limit = 50) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM notification WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function mark_notification_read($pdo, $notification_id, $user_id) {
    try {
        $stmt = $pdo->prepare("UPDATE notification SET is_read = TRUE WHERE notification_id = ? AND user_id = ?");
        $stmt->execute([$notification_id, $user_id]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
?>
