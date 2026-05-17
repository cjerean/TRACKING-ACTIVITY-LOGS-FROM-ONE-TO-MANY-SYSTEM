<?php
require_once 'core/dbconfig.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'core/models.php';

// Fetch more logs for the dedicated page, or implement pagination if needed
// For now, let's fetch the top 100 recent activities
$audit_logs = User::getAuditLog($pdo, 100);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus | Activity Logs</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-6">
    <div class="max-w-6xl mx-auto space-y-8">
        <header class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <a href="index.php"><h1 class="text-4xl font-bold text-gray-900">Nexus</h1></a>
                <p class="text-gray-600 mt-2">Activity Logs</p>
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
                <a href="index.php" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Home
                </a>
            </div>
        </header>

        <section class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">System Activity</h2>
            <p class="text-gray-500 mb-6 text-sm">Tracking all user interactions (CREATE, READ, UPDATE, DELETE) across the system. Editing or deleting these records is not permitted.</p>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Table</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (count($audit_logs) > 0): ?>
                            <?php foreach ($audit_logs as $log): ?>
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($log['username']); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?php
                                            switch($log['action']) {
                                                case 'INSERT': echo 'bg-green-100 text-green-800'; break;
                                                case 'UPDATE': echo 'bg-blue-100 text-blue-800'; break;
                                                case 'DELETE': echo 'bg-red-100 text-red-800'; break;
                                                case 'READ': echo 'bg-purple-100 text-purple-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>">
                                            <?php echo htmlspecialchars($log['action']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($log['table_name']); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 max-w-md">
                                        <?php echo htmlspecialchars($log['action_details'] ?? 'N/A'); ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-500 text-sm italic">
                                    No activity logs found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
