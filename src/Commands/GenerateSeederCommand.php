<?php

namespace RobinNcode\LaravelDbCraft\Commands;

use Illuminate\Console\Command;
use RobinNcode\LaravelDbCraft\Services\SeederGenerator;

class GenerateSeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:seeder {table?} {--connection=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate seeder files from existing database tables with data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $table = $this->argument('table');
        $connection = $this->option('connection') ?? config('db-craft.connection');

        $generator = new SeederGenerator($connection);

        $this->info('🚀 Starting seeder generation...');
        $this->newLine();

        try {
            if ($table) {
                $this->info("Generating seeder for table: {$table}");

                $recordCount = $generator->getTableRecordCount($table);

                if ($recordCount === 0) {
                    $this->warn("⚠️  Table '{$table}' has no data. Skipping seeder generation.");
                    return Command::SUCCESS;
                }

                $this->info("Found {$recordCount} records in table '{$table}'");
                $file = $generator->generateForTable($table);
                $this->info("✅ Seeder created: {$file}");
            } else {
                $this->info("Generating seeders for all tables...");
                $files = $generator->generateAll();

                foreach ($files as $tableName => $file) {
                    $this->info("✅ Seeder created for '{$tableName}': {$file}");
                }

                $this->newLine();
                $this->info("Total seeders generated: " . count($files));
            }

            $this->newLine();
            $this->info('🎉 Seeder generation completed successfully!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());

            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }
}