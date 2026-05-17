<?php
/**
 * Main application page for managing rooms and players.
 *
 * Users must be authenticated to create, update, and delete rooms or players.
 */
require_once 'core/dbconfig.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'core/models.php';

$edit_room = isset($_GET['edit_room']) ? (int) $_GET['edit_room'] : null;
$edit_player = isset($_GET['edit_player']) ? (int) $_GET['edit_player'] : null;

$rooms = Room::getAll($pdo);
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus | Room & Player Management</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 text-gray-800 font-sans p-6">
    <div class="max-w-6xl mx-auto space-y-8">
        <header class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <a href="index.php"><h1 class="text-4xl font-bold text-gray-900">Nexus</h1></a>
                <p class="text-gray-600 mt-2">Room & Player Management</p>
            </div>
            
            <form action="search.php" method="GET" class="flex-1 w-full max-w-md mx-auto md:mx-4">
                <div class="relative flex items-center">
                    <input type="text" name="q" placeholder="Search rooms or players..." required class="w-full border border-gray-300 rounded-full px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="absolute left-3 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <button type="submit" class="hidden">Search</button>
                </div>
            </form>

            <div class="flex items-center gap-4 w-full md:w-auto justify-end">
                <a href="activity_logs.php" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Activity Logs
                </a>
                <div class="text-right hidden sm:block">
                    <p class="text-sm text-gray-600">Welcome,</p>
                    <p class="font-semibold leading-tight"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                </div>
                <form action="core/formhandler.php" method="post" class="inline">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded transition duration-200">
                        Logout
                    </button>
                </form>
            </div>
        </header>

            <section class="bg-white p-6 rounded-lg shadow-md mb-8">
                <h2 class="text-2xl font-semibold mb-4">Create New Room</h2>
                <form action="core/formhandler.php" method="post" class="flex flex-col md:flex-row gap-4 items-end">
                    <input type="hidden" name="action" value="create_room">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Room Name</label>
                        <input type="text" name="room_name" placeholder="E.g. The Matrix" required
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Theme</label>
                        <input type="text" name="theme" placeholder="E.g. Sci-Fi"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Capacity</label>
                        <input type="number" name="max_capacity" placeholder="10" required
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded transition duration-200">Create
                        Room</button>
                </form>
            </section>

            <?php if ($edit_room): ?>
                <?php $room = Room::getById($pdo, $edit_room); ?>
                <section class="bg-blue-50 border border-blue-200 p-6 rounded-lg shadow-sm mb-8 relative">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-blue-800">Editing Room:
                            <?php echo htmlspecialchars($room['room_name']); ?></h3>
                        <a href="index.php"
                            class="text-gray-500 hover:text-gray-800 text-2xl font-bold leading-none">&times;</a>
                    </div>
                    <form action="core/formhandler.php" method="post" class="flex flex-col md:flex-row gap-4 items-end">
                        <input type="hidden" name="action" value="update_room">
                        <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Room Name</label>
                            <input type="text" name="room_name" value="<?php echo htmlspecialchars($room['room_name']); ?>"
                                required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Theme</label>
                            <input type="text" name="theme" value="<?php echo htmlspecialchars($room['theme']); ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Capacity</label>
                            <input type="number" name="max_capacity" value="<?php echo $room['max_capacity']; ?>" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition duration-200">Save</button>
                            <a href="index.php"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded transition duration-200">Cancel</a>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($rooms as $room): ?>
                    <article class="bg-white rounded-lg shadow-md flex flex-col overflow-hidden">
                        <header class="bg-gray-50 border-b border-gray-200 p-4 flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">
                                    <?php echo htmlspecialchars($room['room_name']); ?></h3>
                                <div class="flex flex-wrap gap-2">
                                    <?php if (!empty($room['theme'])): ?>
                                        <span
                                            class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full font-medium"><?php echo htmlspecialchars($room['theme']); ?></span>
                                    <?php endif; ?>
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">Max:
                                        <?php echo $room['max_capacity']; ?></span>
                                </div>
                            </div>
                            <div class="flex gap-1">
                                <a href="?edit_room=<?php echo $room['room_id']; ?>"
                                    class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded transition duration-200">
                                    Edit
                                </a>
                                <form action="core/formhandler.php" method="post" class="inline">
                                    <input type="hidden" name="action" value="delete_room">
                                    <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                                    <button type="submit"
                                        class="text-sm bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 rounded transition duration-200"
                                        onclick="return confirm('Are you sure you want to delete this room?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </header>

                        <div class="p-4 flex-grow">
                            <h4 class="text-sm font-semibold text-gray-500 mb-3 uppercase tracking-wider">Players</h4>
                            <div class="space-y-2">
                                <?php $players = Player::getByRoom($pdo, $room['room_id']); ?>
                                <?php if (empty($players)): ?>
                                    <p class="text-gray-400 text-sm italic">No players listed yet.</p>
                                <?php else: ?>
                                    <?php foreach ($players as $player): ?>
                                        <div
                                            class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-100">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                                                    <?php echo strtoupper(substr(htmlspecialchars($player['player_name']), 0, 1)); ?>
                                                </div>
                                                <div class="leading-tight">
                                                    <div class="text-sm font-bold text-gray-800">
                                                        <?php echo htmlspecialchars($player['player_name']); ?></div>
                                                    <div class="text-xs text-gray-500">
                                                        <?php echo htmlspecialchars($player['email']); ?></div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <a href="?edit_player=<?php echo $player['player_id']; ?>"
                                                    class="text-xs text-blue-600 hover:underline">Edit</a>
                                                <form action="core/formhandler.php" method="post" class="inline">
                                                    <input type="hidden" name="action" value="delete_player">
                                                    <input type="hidden" name="player_id"
                                                        value="<?php echo $player['player_id']; ?>">
                                                    <button type="submit" class="text-xs text-red-600 hover:underline"
                                                        onclick="return confirm('Remove player?')">Remove</button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-200">
                            <form action="core/formhandler.php" method="post" class="flex flex-col sm:flex-row gap-2">
                                <input type="hidden" name="action" value="create_player">
                                <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                                <input type="text" name="player_name" placeholder="Player Name" required
                                    class="flex-1 min-w-0 text-sm border border-gray-300 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <input type="email" name="email" placeholder="Email"
                                    class="flex-1 min-w-0 text-sm border border-gray-300 rounded px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <button type="submit"
                                    class="shrink-0 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-3 py-1.5 rounded transition duration-200">Add</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </main>



        <?php if ($edit_player): ?>
            <?php $player = Player::getById($pdo, $edit_player); ?>
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-800">Edit Player</h3>
                        <a href="index.php" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</a>
                    </div>
                    <form action="core/formhandler.php" method="post" class="p-6 space-y-4">
                        <input type="hidden" name="action" value="update_player">
                        <input type="hidden" name="player_id" value="<?php echo $player['player_id']; ?>">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Player Name</label>
                            <input type="text" name="player_name"
                                value="<?php echo htmlspecialchars($player['player_name']); ?>" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($player['email']); ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="pt-2 flex gap-3">
                            <button type="submit"
                                class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded transition duration-200">Save
                                Changes</button>
                            <a href="index.php"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 text-center rounded transition duration-200">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>