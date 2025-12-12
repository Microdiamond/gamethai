<?php
// score_api.php - Handle Score Saving/Loading with SQLite

$db_file = __DIR__ . '/gamethai_scores.db';

try {
    // Connect to SQLite database
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables if not exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS spelling_scores (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        player_name TEXT NOT NULL,
        score INTEGER NOT NULL, -- time in ms (lower is better)
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS hangman_scores (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        player_name TEXT NOT NULL,
        score INTEGER NOT NULL, -- attempts (lower is better)
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS categorizer_scores (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        player_name TEXT NOT NULL,
        score INTEGER NOT NULL, -- correct answers (higher is better)
        time_ms INTEGER NOT NULL, -- time in ms
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

} catch (PDOException $e) {
    die(json_encode(['error' => "Database connection failed: " . $e->getMessage()]));
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$game = $_GET['game'] ?? $_POST['game'] ?? '';

if ($action === 'save') {
    // Save Score
    $player_name = trim($_POST['player_name'] ?? '');
    if (empty($player_name)) {
        $player_name = 'Player-' . substr(uniqid(), -4);
    }

    try {
        if ($game === 'spelling') {
            $score = (int)$_POST['score']; // time_ms
            $stmt = $pdo->prepare("INSERT INTO spelling_scores (player_name, score) VALUES (:name, :score)");
            $stmt->execute([':name' => $player_name, ':score' => $score]);
        } elseif ($game === 'hangman') {
            $score = (int)$_POST['score']; // attempts
            $stmt = $pdo->prepare("INSERT INTO hangman_scores (player_name, score) VALUES (:name, :score)");
            $stmt->execute([':name' => $player_name, ':score' => $score]);
        } elseif ($game === 'categorizer') {
            $score = (int)$_POST['score']; // points
            $time_ms = (int)$_POST['time_ms'];
            $stmt = $pdo->prepare("INSERT INTO categorizer_scores (player_name, score, time_ms) VALUES (:name, :score, :time)");
            $stmt->execute([':name' => $player_name, ':score' => $score, ':time' => $time_ms]);
        } else {
            echo json_encode(['error' => 'Invalid game type']);
            exit;
        }
        echo json_encode(['success' => true, 'player_name' => $player_name]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }

} elseif ($action === 'load') {
    // Load Top 10 Scores
    try {
        if ($game === 'spelling') {
            $stmt = $pdo->query("SELECT player_name, score FROM spelling_scores ORDER BY score ASC LIMIT 10");
        } elseif ($game === 'hangman') {
            $stmt = $pdo->query("SELECT player_name, score FROM hangman_scores ORDER BY score ASC LIMIT 10");
        } elseif ($game === 'categorizer') {
            $stmt = $pdo->query("SELECT player_name, score, time_ms FROM categorizer_scores ORDER BY score DESC, time_ms ASC LIMIT 10");
        } else {
            echo json_encode(['error' => 'Invalid game type']);
            exit;
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} elseif ($action === 'load_all') {
    // Load ALL Scores for Management (with IDs)
    try {
        if ($game === 'spelling') {
            $stmt = $pdo->query("SELECT id, player_name, score, created_at FROM spelling_scores ORDER BY created_at DESC");
        } elseif ($game === 'hangman') {
            $stmt = $pdo->query("SELECT id, player_name, score, created_at FROM hangman_scores ORDER BY created_at DESC");
        } elseif ($game === 'categorizer') {
            $stmt = $pdo->query("SELECT id, player_name, score, time_ms, created_at FROM categorizer_scores ORDER BY created_at DESC");
        } else {
            echo json_encode(['error' => 'Invalid game type']);
            exit;
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} elseif ($action === 'delete') {
    // Delete Score
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['error' => 'Invalid ID']);
        exit;
    }
    
    try {
        if ($game === 'spelling') {
            $stmt = $pdo->prepare("DELETE FROM spelling_scores WHERE id = :id");
        } elseif ($game === 'hangman') {
            $stmt = $pdo->prepare("DELETE FROM hangman_scores WHERE id = :id");
        } elseif ($game === 'categorizer') {
            $stmt = $pdo->prepare("DELETE FROM categorizer_scores WHERE id = :id");
        } else {
            echo json_encode(['error' => 'Invalid game type']);
            exit;
        }
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid action']);
}
?>
