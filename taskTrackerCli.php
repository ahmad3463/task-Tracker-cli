<?php



$file = "tasks.json";


function loadTasks($file) {
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([], JSON_PRETTY_PRINT));
    }
    $data = file_get_contents($file);
    return json_decode($data, true) ?? [];
}


function saveTasks($file, $tasks) {
    file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
}


function getNextId($tasks) {
    $ids = array_column($tasks, "id");
    return empty($ids) ? 1 : max($ids) + 1;
}


function printTask($task) {
    echo "[{$task['id']}] {$task['description']} | Status: {$task['status']} | Created: {$task['createdAt']} | Updated: {$task['updatedAt']}\n";
}

$tasks = loadTasks($file);

if ($argc < 2) {
    echo "Usage: php tracker.php [add|update|delete|mark-in-progress|mark-done|list] [arguments]\n";
    exit;
}

$command = $argv[1];
$now = date("Y-m-d H:i:s");

switch ($command) {
    case "add":
        if ($argc < 3) {
            echo "Please provide a task description.\n";
            exit;
        }
        $description = $argv[2];
        $task = [
            "id" => getNextId($tasks),
            "description" => $description,
            "status" => "todo",
            "createdAt" => $now,
            "updatedAt" => $now
        ];
        $tasks[] = $task;
        saveTasks($file, $tasks);
        echo "Task added successfully (ID: {$task['id']})\n";
        break;

    case "update":
        if ($argc < 4) {
            echo "Usage: php tracker.php update [id] [new description]\n";
            exit;
        }
        $id = (int)$argv[2];
        $newDescription = $argv[3];
        $found = false;
        foreach ($tasks as &$task) {
            if ($task["id"] === $id) {
                $task["description"] = $newDescription;
                $task["updatedAt"] = $now;
                $found = true;
                break;
            }
        }
        if ($found) {
            saveTasks($file, $tasks);
            echo "Task $id updated successfully.\n";
        } else {
            echo "Task with ID $id not found.\n";
        }
        break;

    case "delete":
        if ($argc < 3) {
            echo "Usage: php tracker.php delete [id]\n";
            exit;
        }
        $id = (int)$argv[2];
        $before = count($tasks);
        $tasks = array_filter($tasks, fn($t) => $t["id"] !== $id);
        if (count($tasks) < $before) {
            saveTasks($file, $tasks);
            echo "Task $id deleted successfully.\n";
        } else {
            echo "Task with ID $id not found.\n";
        }
        break;

    case "mark-in-progress":
    case "mark-done":
        if ($argc < 3) {
            echo "Usage: php tracker.php $command [id]\n";
            exit;
        }
        $id = (int)$argv[2];
        $status = $command === "mark-in-progress" ? "in-progress" : "done";
        $found = false;
        foreach ($tasks as &$task) {
            if ($task["id"] === $id) {
                $task["status"] = $status;
                $task["updatedAt"] = $now;
                $found = true;
                break;
            }
        }
        if ($found) {
            saveTasks($file, $tasks);
            echo "Task $id marked as $status.\n";
        } else {
            echo "Task with ID $id not found.\n";
        }
        break;

    case "list":
        $filter = $argv[2] ?? "all";
        $filteredTasks = $tasks;
        if ($filter === "done" || $filter === "todo" || $filter === "in-progress") {
            $filteredTasks = array_filter($tasks, fn($t) => $t["status"] === $filter);
        }
        if (empty($filteredTasks)) {
            echo "No tasks found for filter: $filter\n";
        } else {
            foreach ($filteredTasks as $task) {
                printTask($task);
            }
        }
        break;

    default:
        echo "Unknown command: $command\n";
        echo "Available: add, update, delete, mark-in-progress, mark-done, list\n";
        break;
}
