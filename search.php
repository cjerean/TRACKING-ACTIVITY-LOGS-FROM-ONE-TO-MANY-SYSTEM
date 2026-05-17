<?php
/**
 * Search page for rooms and players.
 *
 * Queries both models and records search activity in the audit log.
 */
require_once 'core/dbconfig.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'core/models.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$user_id = $_SESSION['user_id'];

$rooms = [];
$players = [];

if ($keyword !== '') {
    $rooms = Room::search($pdo, $keyword, $user_id);
    $players = Player::search($pdo, $keyword, $user_id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus | Search Results</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-6">
    <div class="max-w-6xl mx-auto space-y-8">
        <header class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <a href="index.php"><h1 class="text-4xl font-bold text-gray-900">Nexus</h1></a>
                <p class="text-gray-600 mt-2">Search Results</p>
            </div>
            
            <form action="search.php" method="GET" class="flex-1 w-full max-w-md mx-auto md:mx-4">
                <div class="relative flex items-center">
                    <input type="text" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Search rooms or players..." required class="w-full border border-gray-300 rounded-full px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="absolute left-3 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <button type="submit" class="hidden">Search</button>
                </div>
            </form>

            <div class="flex items-center gap-4 w-full md:w-auto justify-end">
                <a href="index.php" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Back to Home</a>
                <a href="activity_logs.php" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Activity Logs
                </a>
            </div>
        </header>

        <?php if ($keyword !== ''): ?>
            <h2 class="text-2xl font-semibold mb-4">Search results for "<span class="text-blue-600"><?php echo htmlspecialchars($keyword); ?></span>"</h2>
            
            <section class="bg-white p-6 rounded-lg shadow-md mb-8">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Rooms (<?php echo count($rooms); ?>)</h3>
                <?php if (count($rooms) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Theme</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Capacity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($rooms as $room): ?>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($room['room_name']); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($room['theme'] ?? 'None'); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($room['max_capacity']); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            <a href="index.php?edit_room=<?php echo $room['room_id']; ?>" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">No rooms matched your search.</p>
                <?php endif; ?>
            </section>

            <section class="bg-white p-6 rounded-lg shadow-md mb-8">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Players (<?php echo count($players); ?>)</h3>
                <?php if (count($players) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Player Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($players as $player): ?>
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($player['player_name']); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($player['email'] ?? 'None'); ?></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"><?php echo htmlspecialchars($player['room_name'] ?? 'Unknown'); ?></span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            <a href="index.php?edit_player=<?php echo $player['player_id']; ?>" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">No players matched your search.</p>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h3 class="text-xl font-medium text-gray-600">Enter a keyword to search</h3>
                <p class="text-gray-500 mt-2">Search across rooms and players to find matching records.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
