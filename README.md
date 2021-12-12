### Coding Task - XML Data Importer

##Goal
We would like to see a command-line program, based on the Symfony CLI component (https://symfony.com/doc/current/components/console.html). The program should process a local or remote XML file and store the data in a CSV file. Build the code in a way that we can easily add another storage adapter if we want to store the data somewhere else (SQlite, MySQL, ...)

## Specifications
1.	The program should read in a local or remote xml file (configurable as a parameter)
2.	Errors should be written to a logfile

## How to Setup
1. Clone the repo
2. Install dependency by running ```composer install ``` from root directory
3. Setup the correct path in ```.env``` file STORAGE_ENGINE="csv" and STORAGE_PATH="Your local path"
4. Execute the CLI command

Navigate to project root directory and run below command

```php application.php app:xml-reader --source=local --source_path=/Users/your-path/coffee_feed.xml``` 

```php application.php app:xml-reader --source=url --source_path=https://github.com/coffee_feed.xml``` 

## Optional - Run Test case
Navigate to project root directory and run below command

```php ./vendor/bin/phpunit tests/Command/XMLReaderCommandTest.php```
