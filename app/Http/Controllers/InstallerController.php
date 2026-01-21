<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallerController extends Controller
{
    /**
     * Show the installer form if not installed
     */
    public function show()
    {
        if ($this->isInstalled()) {
            return redirect()->route('claims.index');
        }

        return view('installer.index');
    }

    /**
     * Process the installer form
     */
    public function store(Request $request)
    {
        if ($this->isInstalled()) {
            return redirect()->route('claims.index');
        }

        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'app_url' => ['required', 'url'],
            'db_host' => ['required', 'string'],
            'db_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'db_database' => ['required', 'string'],
            'db_username' => ['required', 'string'],
            'db_password' => ['nullable', 'string'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            // Test database connection with provided credentials
            if (!$this->testDatabaseConnection($validated)) {
                return back()->withInput()->with('error', 'Failed to connect to database. Please check your database credentials.');
            }

            // Update PHP config files with installation settings
            $this->updateConfigFiles($validated);

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // Create admin user
            $this->createAdminUser($validated);

            // Mark as installed
            $this->markAsInstalled();

            return redirect()->route('claims.index')->with('success', 'Installation completed successfully!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Installation failed: ' . $e->getMessage());
        }
    }

    /**
     * Check if application is already installed
     */
    private function isInstalled(): bool
    {
        $configPath = config_path('installer.php');
        return File::exists($configPath);
    }

    /**
     * Test database connection with provided credentials
     */
    private function testDatabaseConnection(array $config): bool
    {
        try {
            $conn = new \PDO(
                "mysql:host={$config['db_host']}:{$config['db_port']};dbname={$config['db_database']}",
                $config['db_username'],
                $config['db_password'] ?? ''
            );
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $conn->query('SELECT 1');
            $conn = null;
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Update PHP configuration files with database and application settings
     */
    private function updateConfigFiles(array $config): void
    {
        // Update database config
        $databaseConfigPath = config_path('database.php');
        $databaseConfig = File::get($databaseConfigPath);
        
        // Replace env() calls with hardcoded values for mysql connection
        $databaseConfig = str_replace(
            "'host' => env('DB_HOST', 'localhost'),",
            "'host' => '{$config['db_host']}'.",
            $databaseConfig
        );
        $databaseConfig = str_replace(
            "'port' => env('DB_PORT', 3306),",
            "'port' => {$config['db_port']},",
            $databaseConfig
        );
        $databaseConfig = str_replace(
            "'database' => env('DB_DATABASE', 'vericrown'),",
            "'database' => '{$config['db_database']}'.",
            $databaseConfig
        );
        $databaseConfig = str_replace(
            "'username' => env('DB_USERNAME', 'vericrown'),",
            "'username' => '{$config['db_username']}'.",
            $databaseConfig
        );
        $databaseConfig = str_replace(
            "'password' => env('DB_PASSWORD', ''),",
            "'password' => '{$config['db_password']}'.",
            $databaseConfig
        );
        
        File::put($databaseConfigPath, $databaseConfig);

        // Update app config
        $appConfigPath = config_path('app.php');
        $appConfig = File::get($appConfigPath);
        
        $appConfig = str_replace(
            "'name' => 'VeriCrowd',",
            "'name' => '{$config['app_name']}'.",
            $appConfig
        );
        $appConfig = str_replace(
            "'url' => 'http://localhost',",
            "'url' => '{$config['app_url']}'.",
            $appConfig
        );
        
        File::put($appConfigPath, $appConfig);
    }

    /**
     * Create admin user
     */
    private function createAdminUser(array $config): void
    {
        \App\Models\User::create([
            'name' => $config['admin_name'],
            'email' => $config['admin_email'],
            'password' => bcrypt($config['admin_password']),
        ]);
    }

    /**
     * Mark installation as complete by creating installer config file
     */
    private function markAsInstalled(): void
    {
        $installerConfigPath = config_path('installer.php');
        
        $content = <<<'PHP'
<?php

return [
    'installed' => true,
];
PHP;
        
        File::put($installerConfigPath, $content);
    }
}
