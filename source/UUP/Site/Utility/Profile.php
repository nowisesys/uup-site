<?php

/*
 * Copyright (C) 2017 Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University).
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace UUP\Site\Utility;

/**
 * The profile task.
 */
class Task
{

        /**
         * The task name.
         * @var string 
         */
        public $name;
        /**
         * The task time.
         * @var float 
         */
        public $time;

        /**
         * Constructor.
         * @param string $name The task name.
         */
        public function __construct($name)
        {
                $this->name = $name;
        }

        /**
         * Start timer.
         */
        public function start()
        {
                $this->time = microtime(true);
        }

        /**
         * Stop timer.
         */
        public function stop()
        {
                $this->time = microtime(true) - $this->time;
        }

}

/**
 * Simple system profiler.
 * 
 * <code>
 * $profiler = new Profiler();
 * $profiler->start();                  // Start global profiler
 * 
 * $profiler->push('task1);             // Create task profiler
 * $profiler->start();                  // Start task profiling
 * // ... do something ...
 * $profiler->stop();                   // Stop task profiling
 * 
 * $profiler->stop();                   // Stop global profiler
 * 
 * $time = $profiler->get('task1');     // Get task profile data.
 * $time = $profiler->get();            // Get global profile data.
 * 
 * $time = $profile->data;              // Get all profile data.
 * 
 * </code>
 * 
 * @property-read array $data The profile data.
 *
 * @author Anders Lövgren (Nowise Systems/BMC-IT, Uppsala University)
 * @package UUP
 * @subpackage Site
 */
class Profile
{

        /**
         * The profiling data.
         * @var array 
         */
        private $_data = array();
        /**
         * Profiling task queue.
         * @var array 
         */
        private $_task = array();

        /**
         * Constructor.
         * @param string $name The profiler name.
         */
        public function __construct($name = 'global')
        {
                $this->push($name);
        }

        public function __get($name)
        {
                if ($name == 'data') {
                        return $this->_data;
                }
        }

        /**
         * Push profile task on queue.
         * @param string $name The task name.
         */
        public function push($name)
        {
                array_push($this->_task, new Task($name));
        }

        /**
         * Start profiling.
         */
        public function start()
        {
                $task = end($this->_task);
                $task->start();
        }

        /**
         * Stop profiling.
         */
        public function stop()
        {
                $task = array_pop($this->_task);
                $task->stop();
                $this->_data[$task->name] = $task->time;
        }

        /**
         * Get task profile data.
         * @param string $name The task name.
         * @return float
         */
        public function get($name)
        {
                if (isset($this->_data[$name])) {
                        return $this->_data[$name];
                }
        }

        /**
         * Add task.
         * @param Task $task The profile task.
         */
        public function add($task)
        {
                $this->_data[$task->name] = $task->time;
        }

}
