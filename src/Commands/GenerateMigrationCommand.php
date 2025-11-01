<?php

namespace RobinNcode\LaravelDbCraft\Commands;

use Illuminate\Console\Command;
use RobinNcode\LaravelDbCraft\Services\MigrationGenerator;

/**
 * Command to generate migration files from existing database tables.
 * Can generate for all tables or a specific table.
 * @author RobinNcode
 */
class GenerateMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:migration {table?} {--connection=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate migration files from existing database tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $table = $this->argument('table');
        $connection = $this->option('connection') ?? config('db-craft.connection');

        $generator = new MigrationGenerator($connection);

        $this->info('🚀 Starting migration generation...');
        $this->newLine();

        try {
            if ($table) {
                $this->info("Generating migration for table: {$table}");
                $file = $generator->generateForTable($table);
                $this->info("✅ Migration created: {$file}");
            } else {
                $this->info("Generating migrations for all tables...");
                $files = $generator->generateAll();

                foreach ($files as $file) {
                    $this->info("✅ Migration created: {$file}");
                }

                $this->newLine();
                $this->info("Total migrations generated: " . count($files));
            }

            $this->newLine();
            $this->info('🎉 Migration generation completed successfully!');

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