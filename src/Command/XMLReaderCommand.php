<?php

namespace App\Command;

use Psr\Log\LogLevel;
use App\Library\File\File;
use App\Library\Storage\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class XMLReaderCommand
 * @package App\Command
 *
 * HOW TO EXECUTE:
 * php application.php app:xml-reader --source=local --source_path=/Users/coffee_feed.xml
 * php application.php app:xml-reader --source=url --source_path=https://github.com/coffee_feed.xml
 */
class XMLReaderCommand extends Command
{
    protected static $defaultName = 'app:xml-reader';
    protected static $defaultDescription = 'Read an XML file from local path or remote url.';

    /** @var array */
    protected $errors = [];

    /**
     * We configure here the required options for the command
     */
    protected function configure(): void
    {
        $this->addOption(
                'source',
                null,
                InputOption::VALUE_REQUIRED,
                'What is the source of input? Options are [local, url]'
            )->addOption(
                'source_path',
                null,
                InputOption::VALUE_REQUIRED,
                'The path of source either local file path or remote url'
            )->addOption(
                'storage',
                null,
                InputOption::VALUE_OPTIONAL,
                'The storage for the xml data',
                'csv'
        );
    }

    /**
     * Function to validate the options values and return error if any
     * @param InputInterface $input
     *
     * @return string
     */
    protected function validateOptionsAreSet(InputInterface $input): string
    {
        $errors = [];
        $options = $this->getDefinition()->getOptions();
        foreach ($options as $option) {
            $name = $option->getName();
            $value = $input->getOption($name);
            if ($option->isValueRequired() && ($value === null || $value === '')) {
                $errors[] = sprintf('The required option --%s is not set or is empty', $name);
            }
        }

        if (count($errors)) {
            return implode("\n", $errors);
        }

        return "";
    }

    /**
     * Main execution start here
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Initialize the pretty logger
        $ConsoleLogger = new ConsoleLogger($output, [
            LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::INFO   => OutputInterface::VERBOSITY_NORMAL,
        ]);

        $output->writeln('<info>Processing started..</info>');

        // Run the options for validation
        $error = $this->validateOptionsAreSet($input);
        if (!empty($error)) {
            $ConsoleLogger->error($error);
            $this->logError($error);

            return Command::INVALID;
        }

        // Capture the option values to be used
        $source   = $input->getOption('source');
        $resource = $input->getOption('source_path');
        $storage  = $input->getOption('storage');

        // ACTUAL PROCESSING STARTS NOW

        try {
            // Create the instance of source
            $SourceFile = File::get($source, $resource);

            // Create the instance of storage engine
            $Storage = Storage::get($storage);

            // Load the data from source file
            $SourceFile->load();

            // Write back the data from source file to storage engine
            $Storage->write($SourceFile->getData(), true);

            $ConsoleLogger->info('Process completed successfully! ');
            $ConsoleLogger->info(count($SourceFile->getData()) . " rows imported successfully!");
            $ConsoleLogger->info("Data saved at " . $Storage->getOutputPath());

        } catch (\Exception $exception) {
            $ConsoleLogger->error($exception->getMessage());
            $this->logError($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Helper function to log the error
     * @param $message
     */
    private function logError(string $message) {
        $this->errors[] = date('c') . " [Error] " . $message;
    }

    /**
     * Logging the errors at the end
     */
    public function __destruct() {
        global $argv;
        if (!empty($this->errors)) {
            error_log(date('c') . " [Command] " . implode(', ', $argv) . PHP_EOL);
        }

        foreach ($this->errors as $error) {
            error_log($error . PHP_EOL);
        }
    }
}
