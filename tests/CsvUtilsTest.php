<?php

use PHPUnit\Framework\TestCase;
use PriceTracker\Utils\CsvUtils;

class CsvUtilsTest extends TestCase {
    private $testDir;
    private $csvUtils;

    protected function setUp(): void {
        $this->testDir = __DIR__ . '/data/';
        if (!is_dir($this->testDir)) {
            mkdir($this->testDir, 0777, true);
        }
        $this->csvUtils = new CsvUtils($this->testDir);
    }

    protected function tearDown(): void {
        // Cleanup files
        if (is_dir($this->testDir)) {
            $files = glob($this->testDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
            rmdir($this->testDir);
        }
    }

    public function testWriteAndReadLastLine() {
        $filename = 'test.csv';
        $data1 = ['2023-01-01', '100', '101'];
        $data2 = ['2023-01-02', '102', '103'];

        $this->csvUtils->writeCsvToFile($filename, $data1);
        $this->csvUtils->writeCsvToFile($filename, $data2);

        $lastLine = $this->csvUtils->getLastLineFromCsvAsArray($filename);
        $this->assertEquals($data2, $lastLine);
    }

    public function testReadLastLineWithTrailingNewline() {
        $filename = 'newline.csv';
        // Simulating manual file creation with newline
        file_put_contents($this->testDir . $filename, "h1,h2\nval1,val2\n");

        $lastLine = $this->csvUtils->getLastLineFromCsvAsArray($filename);
        $this->assertEquals(['val1', 'val2'], $lastLine);
    }

    public function testReadSingleLine() {
        $filename = 'single.csv';
        $data = ['2023-01-01', '100'];
        $this->csvUtils->writeCsvToFile($filename, $data);

        $lastLine = $this->csvUtils->getLastLineFromCsvAsArray($filename);
        $this->assertEquals($data, $lastLine);
    }
}
