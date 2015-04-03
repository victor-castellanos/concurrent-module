<?php
require_once(dirname(__FILE__) . 'app/vendor/autoload.php');

$version = '0.2.1.0';
$help = "Concurrent Module {$version}
 Type 'php concurrent.php --type=[Master/Slave type] --slaves=20' to run
 Type 'php concurrent.php --help' for help
 --type=[Master/Slave type] *Required*    prefix of your master/slave processes.
 --slaves=[i]               *Required*    number of slaves to start.
 --help                                   displays help text.
 --version                                displays current version
";
$numberOfSlaves = 0;
$masterSlaveType = '';
foreach($argv as $argument) {
    if(preg_match('#--slaves=([0-9]+)#', $argument, $m)) {
        $numberOfSlaves = $m[1];
    }
    if(preg_match('#--type=([A-z]+)#', $argument, $m)) {
        $masterSlaveType = $m[1];
    }
    if($argument == '--help') {
        echo $help;
        exit;
    }
    if($argument == '--version') {
        echo "Concurrent Module v.$version\n";
        exit;
    }
}
if(!$numberOfSlaves) echo "Number of slaves must be supplied. Use --slaves[i] or --help for further help.\n";
if(empty($masterSlaveType)) echo "Type of master/slave must be supplied. Use --type[Master/Slave type] or --help for further help.\n";
if(!$numberOfSlaves || empty($masterSlaveType)) exit;

$prefix = '\Concurrent';
$slaveClass = "{$prefix}\\{$masterSlaveType}SlaveProcess";
$masterClass = "{$prefix}\\{$masterSlaveType}MasterProcess";
if(!class_exists($slaveClass)) {
    echo "Class {$slaveClass} does not exists.\n"; exit;
}
if(!class_exists($masterClass)) {
    echo "Class {$masterClass} does not exists.\n"; exit;
}

set_time_limit(0);
ini_set('memory_limit', '-1');
while($numberOfSlaves-- > 0) {
    $pid = pcntl_fork();
    if(!$pid) {
        /**
         * @var $slave \Concurrent\Processes\Slave\SlaveProcess
         */
        $slave = new $slaveClass;
        while(true) {
            $slave->run();
        }
    }
}
/**
 * @var $master \Concurrent\Processes\Master\MasterProcessInterface
 */
$master = new $masterClass;
while(true) {
    $master->receiveMessage();
}
