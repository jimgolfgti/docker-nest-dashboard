<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCommand extends Command
{
    protected $logger;
    protected $processRunner;
    protected $stageLoader;

    protected function configure()
    {
        $this
            ->setName('fetch')
            ->setDescription('Fetch NEST thermostat data into InfluxDB')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Your Nest username.'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'Your Nest password.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        define('USERNAME', $input->getArgument('username'));
        define('PASSWORD', $input->getArgument('password'));

        $this->fetch($output);
    }

    public function fetch(OutputInterface $output)
    {
        $nest = new \Nest();
        $info = $nest->getDeviceInfo();
        $temperature = $info->current_state->temperature;
        $humidity = $info->current_state->humidity;
        $state = $info->target->mode;

        $output->writeln("Temperature: ".$temperature);
        $output->writeln("Humidity: ".$humidity);
        $output->writeln("State: ".$state);

        try {
            $influxDBClient = new \crodas\InfluxPHP\Client("influxdb", 8086, "root", "root");
            $db = $influxDBClient->nest;
            $db->insert("temperature", [ 'fields' => array('value' => $temperature)]);
            $db->insert("humidity", [ 'fields' => array('value' => $humidity)]);
            $db->insert("state", [ 'fields' => array('value' => $state)]);
            $output->writeln("Pushed to InfluxDB");
        } catch (\RuntimeException $e) {
            $output->writeln("Waiting for InfluxDB...");
        }
    }
}
