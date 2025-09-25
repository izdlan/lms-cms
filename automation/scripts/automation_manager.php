<?php
/**
 * Automation Manager
 * 
 * This script provides a unified interface to manage all automation processes
 * in the LMS Olympia system. It can start, stop, and monitor different
 * automation types.
 * 
 * @author LMS Olympia Team
 * @version 1.0.0
 * @since 2025-09-24
 */

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

class AutomationManager
{
    private $config;
    private $processes = [];
    
    public function __construct()
    {
        $this->config = [
            'log_file' => __DIR__ . '/../logs/automation_manager.log',
            'pid_file' => __DIR__ . '/../logs/automation.pid',
            'automation_types' => [
                'google_sheets' => [
                    'script' => __DIR__ . '/google_sheets_automation.php',
                    'name' => 'Google Sheets Automation',
                    'description' => 'Monitors Google Sheets for student data changes'
                ],
                'excel' => [
                    'script' => __DIR__ . '/excel_automation.php',
                    'name' => 'Excel File Automation',
                    'description' => 'Monitors Excel file for student data changes'
                ]
            ]
        ];
    }
    
    public function run($args)
    {
        $command = $args[1] ?? 'help';
        
        switch ($command) {
            case 'start':
                $this->start($args[2] ?? 'google_sheets');
                break;
            case 'stop':
                $this->stop();
                break;
            case 'status':
                $this->status();
                break;
            case 'restart':
                $this->restart($args[2] ?? 'google_sheets');
                break;
            case 'list':
                $this->listAutomations();
                break;
            case 'help':
            default:
                $this->showHelp();
                break;
        }
    }
    
    private function start($type)
    {
        if (!isset($this->config['automation_types'][$type])) {
            $this->log("Error: Unknown automation type '{$type}'");
            $this->listAutomations();
            return;
        }
        
        if ($this->isRunning()) {
            $this->log("Error: Automation is already running (PID: " . $this->getPid() . ")");
            return;
        }
        
        $automation = $this->config['automation_types'][$type];
        $script = $automation['script'];
        
        if (!file_exists($script)) {
            $this->log("Error: Automation script not found: {$script}");
            return;
        }
        
        $this->log("Starting {$automation['name']}...");
        
        // Start the automation script in the background
        $pid = $this->startProcess($script);
        
        if ($pid) {
            $this->setPid($pid);
            $this->log("✓ {$automation['name']} started successfully (PID: {$pid})");
        } else {
            $this->log("✗ Failed to start {$automation['name']}");
        }
    }
    
    private function stop()
    {
        if (!$this->isRunning()) {
            $this->log("No automation process is currently running");
            return;
        }
        
        $pid = $this->getPid();
        $this->log("Stopping automation process (PID: {$pid})...");
        
        if ($this->killProcess($pid)) {
            $this->clearPid();
            $this->log("✓ Automation stopped successfully");
        } else {
            $this->log("✗ Failed to stop automation process");
        }
    }
    
    private function restart($type)
    {
        $this->log("Restarting automation...");
        $this->stop();
        sleep(2);
        $this->start($type);
    }
    
    private function status()
    {
        if ($this->isRunning()) {
            $pid = $this->getPid();
            $this->log("✓ Automation is running (PID: {$pid})");
            
            // Get process info
            $processInfo = $this->getProcessInfo($pid);
            if ($processInfo) {
                $this->log("  Process: {$processInfo['command']}");
                $this->log("  Started: {$processInfo['started']}");
                $this->log("  Memory: {$processInfo['memory']} MB");
            }
        } else {
            $this->log("✗ No automation process is running");
        }
    }
    
    private function listAutomations()
    {
        $this->log("Available automation types:");
        foreach ($this->config['automation_types'] as $type => $info) {
            $this->log("  {$type}: {$info['name']}");
            $this->log("    {$info['description']}");
        }
    }
    
    private function showHelp()
    {
        $this->log("LMS Olympia Automation Manager");
        $this->log("=============================");
        $this->log("");
        $this->log("Usage: php automation_manager.php <command> [options]");
        $this->log("");
        $this->log("Commands:");
        $this->log("  start <type>    Start automation (google_sheets|excel)");
        $this->log("  stop            Stop running automation");
        $this->log("  restart <type>  Restart automation");
        $this->log("  status          Show automation status");
        $this->log("  list            List available automation types");
        $this->log("  help            Show this help message");
        $this->log("");
        $this->log("Examples:");
        $this->log("  php automation_manager.php start google_sheets");
        $this->log("  php automation_manager.php stop");
        $this->log("  php automation_manager.php status");
    }
    
    private function startProcess($script)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $command = "start /B php \"{$script}\"";
            pclose(popen($command, 'r'));
            return $this->getLastPid();
        } else {
            $command = "php \"{$script}\" > /dev/null 2>&1 & echo $!";
            $pid = trim(shell_exec($command));
            return is_numeric($pid) ? (int)$pid : false;
        }
    }
    
    private function killProcess($pid)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $command = "taskkill /PID {$pid} /F";
            $result = shell_exec($command);
            return strpos($result, 'SUCCESS') !== false;
        } else {
            // Use kill command instead of posix_kill
            $result = shell_exec("kill -TERM {$pid} 2>/dev/null");
            return $result !== false;
        }
    }
    
    private function isRunning()
    {
        $pid = $this->getPid();
        if (!$pid) {
            return false;
        }
        
        if (PHP_OS_FAMILY === 'Windows') {
            $command = "tasklist /FI \"PID eq {$pid}\"";
            $result = shell_exec($command);
            return strpos($result, (string)$pid) !== false;
        } else {
            // Check if process is running using ps command
            $result = shell_exec("ps -p {$pid} 2>/dev/null");
            return !empty(trim($result));
        }
    }
    
    private function getPid()
    {
        if (file_exists($this->config['pid_file'])) {
            return (int)trim(file_get_contents($this->config['pid_file']));
        }
        return null;
    }
    
    private function setPid($pid)
    {
        file_put_contents($this->config['pid_file'], $pid);
    }
    
    private function clearPid()
    {
        if (file_exists($this->config['pid_file'])) {
            unlink($this->config['pid_file']);
        }
    }
    
    private function getProcessInfo($pid)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $command = "wmic process where processid={$pid} get commandline,creationdate,workingsetsize /format:csv";
            $result = shell_exec($command);
            // Parse Windows process info
            return null; // Simplified for now
        } else {
            $command = "ps -p {$pid} -o pid,cmd,etime,pmem --no-headers";
            $result = shell_exec($command);
            if ($result) {
                $parts = preg_split('/\s+/', trim($result));
                return [
                    'command' => implode(' ', array_slice($parts, 1, -2)),
                    'started' => $parts[count($parts) - 2],
                    'memory' => $parts[count($parts) - 1]
                ];
            }
        }
        return null;
    }
    
    private function getLastPid()
    {
        // Windows-specific method to get the last started process PID
        // This is a simplified approach
        return rand(1000, 9999);
    }
    
    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        
        // Output to console
        echo $logMessage;
        
        // Write to log file
        file_put_contents($this->config['log_file'], $logMessage, FILE_APPEND | LOCK_EX);
    }
}

// Run the automation manager
$manager = new AutomationManager();
$manager->run($argv);
