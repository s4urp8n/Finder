<?php

use Zver\Finder;

class FinderCest
{
    
    public $testDirectoriesDepth = 40;
    
    public function _before()
    {
        $this->_createTestDirectories();
    }
    
    public function _createTestDirectories()
    {
        $mostDepthDirectory = __DIR__ . str_repeat(DIRECTORY_SEPARATOR . 'test', $this->testDirectoriesDepth);
        if (!file_exists($mostDepthDirectory))
        {
            mkdir($mostDepthDirectory, 0777, true);
        }
        
        for ($i = $this->testDirectoriesDepth; $i > 0; $i--)
        {
            $testFile = __DIR__ . str_repeat(DIRECTORY_SEPARATOR . 'test', $i) . DIRECTORY_SEPARATOR . 'test.txt';
            touch($testFile);
        }
    }
    
    public function _after()
    {
        $this->_deleteTestDirectories();
    }
    
    public function _deleteTestDirectories()
    {
        for ($i = $this->testDirectoriesDepth; $i > 0; $i--)
        {
            $testFile = __DIR__ . str_repeat(DIRECTORY_SEPARATOR . 'test', $i) . DIRECTORY_SEPARATOR . 'test.txt';
            $testDir = __DIR__ . str_repeat(DIRECTORY_SEPARATOR . 'test', $i);
            unlink($testFile);
            rmdir($testDir);
        }
    }
    
    public function testInstance(UnitTester $I)
    {
        Finder::instance();
        $I->assertEquals(gettype(Finder::instance()), 'object');
        $I->assertEquals(get_class(Finder::instance()), 'Zver\Finder');
    }
    
    public function testDirectoriesSetAndGet(UnitTester $I)
    {
        $object = Finder::instance();
        $I->assertEquals($object->getDirectories(), []);
        
        $object->addDirectory(__DIR__);
        $object->addDirectories(__DIR__);
        $object->addDirectories([__DIR__, __DIR__]);
        
        $I->assertEquals($object->getDirectories(), [__DIR__]);
        
        $object->addDirectory(__DIR__ . '/../');
        $object->addDirectory(__DIR__ . '/../../../');
        $object->addDirectory(__DIR__ . '/../');
        $object->addDirectories([__DIR__, __DIR__ . '/../../../']);
        $object->addDirectories([__DIR__, __DIR__ . '/../../../']);
        $object->addDirectories([__DIR__, __DIR__ . '/../../../']);
        $object->addDirectories([__DIR__, __DIR__ . '/../../../']);
        $object->addDirectories([__DIR__, __DIR__ . '/../../../']);
        
        $I->assertEquals(
            $object->getDirectories(), [
                                         __DIR__,
                                         realpath(__DIR__ . '/../'),
                                         realpath(__DIR__ . '/../../../'),
                                     ]
        );
        
        $object->addDirectoryFirst(__DIR__ . '/../../../../../');
        $object->addDirectory(__DIR__ . '/../../../../../');
        $I->assertEquals(
            $object->getDirectories(), [
                                         realpath(__DIR__ . '/../../../../../'),
                                         __DIR__,
                                         realpath(__DIR__ . '/../'),
                                         realpath(__DIR__ . '/../../../'),
                                     ]
        );
        
        $object->resetDirectories();
        $I->assertEquals($object->getDirectories(), []);
    }
    
    public function testResultsDirectories(UnitTester $I)
    {
        $results = Finder::instance(__DIR__ . '/test/')
                         ->filter(
                             function ($path)
                             {
                                 return is_dir($path);
                             }
                         )
                         ->find();
        
        $I->assertEquals(count($results), $this->testDirectoriesDepth);
    }
    
    public function testResultsFiles(UnitTester $I)
    {
        $results = Finder::instance(__DIR__ . '/test/')
                         ->filter(
                             function ($path)
                             {
                                 return is_file($path);
                             }
                         )
                         ->find();
        
        $I->assertEquals(count($results), $this->testDirectoriesDepth);
    }
    
    public function testResultsReadable(UnitTester $I)
    {
        $results = Finder::instance(__DIR__ . '/test/')
                         ->filter(
                             function ($path)
                             {
                                 return is_readable($path);
                             }
                         )
                         ->find();
        
        $I->assertEquals(count($results), $this->testDirectoriesDepth * 2);
    }
    
    public function testResultPassedToFilter(UnitTester $I)
    {
        $originalValues = [];
        $results = Finder::instance(__DIR__ . '/test/')
                         ->filter(
                             function ($path) use (&$originalValues)
                             {
                                 $originalValues[] = $path;
                
                                 return is_readable($path);
                             }
                         )
                         ->find();
        
        $I->assertEquals(count($originalValues), $this->testDirectoriesDepth * 2);
        $I->assertEquals(count($originalValues), count(array_unique($originalValues)));
    }
    
    public function testResultsFilterFalse(UnitTester $I)
    {
        $results = Finder::instance(__DIR__ . '/test/')
                         ->filter(
                             function ($path)
                             {
                                 return false;
                             }
                         )
                         ->find();
        
        $I->assertEquals($results, []);
    }
    
    public function testResultsLimitFilterFalse(UnitTester $I)
    {
        $results = Finder::instance(__DIR__ . '/test/')
                         ->filter(
                             function ($path)
                             {
                                 return false;
                             }
                         )
                         ->limit(10)
                         ->find();
        
        $I->assertEquals($results, []);
        
        $results = Finder::instance(__DIR__ . '/test/')
                         ->filter(
                             function ($path)
                             {
                                 return false;
                             }
                         )
                         ->find();
        
        $I->assertEquals($results, []);
        
        $results = Finder::instance(__DIR__ . '/test/')
                         ->filter(
                             function ($path)
                             {
                                 return false;
                             }
                         )
                         ->resetFilters()
                         ->find();
        
        $I->assertEquals(count($results), $this->testDirectoriesDepth * 2);
    }
    
    public function testResultsLimitFilterTrue(UnitTester $I)
    {
        $results = Finder::instance(__DIR__ . '/test/')
                         ->filter(
                             function ($path)
                             {
                                 return true;
                             }
                         )
                         ->limit(10)
                         ->find();
        
        $I->assertEquals(count($results), 10);
    }
    
    public function testLimitException(UnitTester $I)
    {
        
        $I->expectException(
            'InvalidArgumentException', function ()
        {
            
            Finder::instance()
                  ->limit('sd3r3rf');
        }
        );
    }
    
    public function testAddDirectoryException(UnitTester $I)
    {
        
        $I->expectException(
            'Exception', function ()
        {
            Finder::instance()
                  ->addDirectory('sd3r3rf');
        }
        );
    }
    
    public function testNoDirectoriesSet(UnitTester $I)
    {
        $results = Finder::instance()
                         ->find();
        
        $I->assertEquals($results, []);
    }
    
}