<?php
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\command\ScheduleAble;

class Hello extends Command implements ScheduleAble
{
    protected function configure()
    {
        $this->setName('hello')
            ->addArgument('name', Argument::OPTIONAL, "your name")
            ->addOption('city', null, Option::VALUE_REQUIRED, 'city name')
            ->setDescription('Say Hello');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));
        $name = $name ?: 'thinkphp';

        if ($input->hasOption('city')) {
            $city = PHP_EOL . 'From ' . $input->getOption('city');
        } else {
            $city = '';
        }

        $output->writeln("Hello," . $name . '!' . $city);
    }

    public function getSchConfigs($configName = NULL)
    {
        $configs = [
            'args' => [],
            'period' => '10',
        ];
        if(empty($configName)){
            return $configs;
        } else {
            return !empty($configs[$configName]) ? $configs[$configName] : FALSE;
        }

    }

}