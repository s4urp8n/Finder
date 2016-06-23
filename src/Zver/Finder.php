<?php

namespace Zver
{
    
    /**
     * Class to find files and folders, apply callbacks to it and etc
     *
     * @package Zver
     */
    class Finder
    {
        
        /**
         * List of directories to search
         *
         * @var array
         */
        protected $directories = [];
        
        /**
         * Current instance filter callback
         *
         * @var array
         */
        protected $filter = null;
        
        /**
         * Current instance limit of results
         *
         * @var null
         */
        protected $limit = null;
        
        /**
         * Current instance results
         *
         * @var array
         */
        protected $results = [];
        
        /**
         * Current instance temporary variable to store directories to scan
         *
         * @var array
         */
        protected $forScanning = [];
        
        /**
         * Create instance of class and set directory to find if it is set
         *
         * @param null|string $directoriesToSearch Directory path to search files or directories
         *
         * @return static
         */
        public static function instance($directoriesToSearch = null)
        {
            $object = new static;
            if (!is_null($directoriesToSearch))
            {
                if (!is_array($directoriesToSearch))
                {
                    $directoriesToSearch = [$directoriesToSearch];
                }
                $object->addDirectories($directoriesToSearch);
            }
            
            return $object;
        }
        
        /**
         * Add multiple directories or directory to scan list
         *
         * @param string|array $directories
         *
         * @return $this
         */
        public function addDirectories($directories)
        {
            if (!is_array($directories))
            {
                $directories = [$directories];
            }
            
            foreach ($directories as $directory)
            {
                $this->addDirectory($directory);
            }
            
            return $this;
        }
        
        /**
         * Add directory to the end of scan list
         *
         * @param $directory
         *
         * @return $this
         * @throws \Exception
         */
        public function addDirectory($directory)
        {
            if ($this->checkDirectory($directory))
            {
                $this->directories[] = $this->getDirectoryPath($directory);
            }
            
            return $this;
        }
        
        /**
         * Check directory for existence and availability in scan directories list
         *
         * @param $directory
         *
         * @return bool
         * @throws \Exception
         */
        protected function checkDirectory($directory)
        {
            //directory exists and is directory
            if (file_exists($directory) && is_dir($directory))
            {
                //directory don't added already
                if (!in_array($this->getDirectoryPath($directory), $this->directories))
                {
                    return true;
                }
                
                return false;
            }
            throw new \Exception('Directory "' . $directory . '" not exists');
        }
        
        /**
         * Get platform realpath
         *
         * @param $directory
         *
         * @return string
         */
        protected function getDirectoryPath($directory)
        {
            return realpath($directory);
        }
        
        /**
         * Add directory to beginning of scan list
         *
         * @param $directory
         *
         * @return $this
         * @throws \Exception
         */
        public function addDirectoryFirst($directory)
        {
            if ($this->checkDirectory($directory))
            {
                $this->directories = array_merge([realpath($directory)], $this->directories);
            }
            
            return $this;
        }
        
        /**
         * Get current directories list
         *
         * @return array
         */
        public function getDirectories()
        {
            return $this->directories;
        }
        
        /**
         * Clear directories list
         */
        public function resetDirectories()
        {
            $this->directories = [];
            
            return $this;
        }
        
        /**
         * Reset filter callback
         *
         * @return $this
         */
        public function resetFilters()
        {
            $this->filter = null;
            
            return $this;
        }
        
        /**
         * Set filter callback
         *
         * @param callable $callback
         *
         * @return $this
         */
        public function filter(callable $callback)
        {
            $this->filter = $callback;
            
            return $this;
        }
        
        /**
         * Run search and return array of results
         *
         * @return array
         */
        public function find($excludeRoot = false)
        {
            
            $this->results = [];
            $this->forScanning = $this->directories;
            
            if (!$excludeRoot)
            {
                //root directories first
                foreach ($this->directories as $dir)
                {
                    $this->process($dir);
                }
            }
            
            while (count($this->forScanning) > 0 && $this->isLimitNotExceeded())
            {
                $path = array_shift($this->forScanning);
                $this->scan($path);
            }
            
            return $this->getResults();
        }
        
        /**
         * Process content to mark as result or don't
         *
         * @param $path
         */
        protected function process($path)
        {
            if (!in_array($path, $this->results))
            {
                if (empty($this->filter) || call_user_func($this->filter, $path))
                {
                    $this->results[] = $path;
                }
            }
        }
        
        /**
         * Check limit exceed
         *
         * @return bool
         */
        protected function isLimitNotExceeded()
        {
            return (($this->limit == 0) || ($this->limit > 0 && count($this->results) < $this->limit));
        }
        
        /**
         * Scan folder and process it's content
         *
         * @param $path
         */
        protected function scan($path)
        {
            $temp = '';
            if (file_exists($path) && is_readable($path))
            {
                $contents = scandir($path, SCANDIR_SORT_NONE);
                array_shift($contents);
                array_shift($contents);
                foreach ($contents as $index => $content)
                {
                    $temp = $path . DIRECTORY_SEPARATOR . $content;
                    $this->process($temp);
                    if (is_dir($temp))
                    {
                        $this->forScanning[] = $temp;
                    }
                }
            }
        }
        
        /**
         * Get results array trimmed if limit exceeded
         *
         * @return array
         */
        protected function getResults()
        {
            if ($this->limit != 0)
            {
                $this->results = array_slice($this->results, 0, $this->limit);
            }
            
            return $this->results;
        }
        
        /**
         * Set limit to results array
         *
         * @param $number
         *
         * @return $this
         */
        public function limit($number)
        {
            if (is_numeric($number) && $number >= 0)
            {
                $this->limit = $number;
                
                return $this;
            }
            throw new \InvalidArgumentException('Limit must numeric and >=0');
        }
        
    }
}
