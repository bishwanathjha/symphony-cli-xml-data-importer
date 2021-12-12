<?php

namespace App\Tests\Command;

use App\Command\XMLReaderCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class XMLReaderCommandTest
 * @package App\Tests\Command
 */
class XMLReaderCommandTest extends TestCase
{
    public function testExecute()
    {
        $ROOT = $_SERVER['PWD'];

        $dotenv = new Dotenv();
        $dotenv->load($ROOT.'/.env');

        $application = new Application();
        $application->add(new XMLReaderCommand());

        $command = $application->find('app:xml-reader');
        $commandTester = new CommandTester($command);

        // Invalid case
        $commandTester->execute([]);
        $this->assertSame(Command::INVALID, $commandTester->getStatusCode());

        // Invalid URL case
        $commandTester->execute([
            '--source' => 'url',
            '--source_path' => 'https://google.com/test.xml',
        ]);
        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        // Success case
        $commandTester->execute([
            // prefix the key with two dashes when passing options,
            '--source' => 'local',
            '--source_path' => $ROOT . '/storage/coffee_feed.xml',
        ]);

        // Success case
        $commandTester->execute([
            // prefix the key with two dashes when passing options,
            '--source' => 'local',
            '--source_path' => $ROOT . '/storage/coffee_feed.xml',
        ]);

        $commandTester->assertCommandIsSuccessful();

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Process completed successfully', $output);
        $this->assertStringContainsString('rows imported successfully', $output);
        $this->assertStringContainsString('Data saved at', $output);

        // Failure case
        $commandTester->execute([
            // prefix the key with two dashes when passing options,
            '--source' => 'local',
            '--source_path' => $ROOT . '/storage/invalid.txt',
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringNotContainsString('Process completed successfully', $output);
        $this->assertStringNotContainsString('rows imported successfully', $output);
        $this->assertStringNotContainsString('Data saved at', $output);
    }
}
