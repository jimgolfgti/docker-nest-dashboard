<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;

class FetchCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fetch')
            ->setDescription('Fetch NEST thermostat data into InfluxDB');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->fetch($output);
    }

    public function fetch(OutputInterface $output)
    {
        $influxDBClient = new \crodas\InfluxPHP\Client("influxdb", 8086, "root", "root");
        $db = $influxDBClient->nest;

        try {
            $this->pushNestData($db);
            $output->writeln("Pushed Nest Data to InfluxDB");
        } catch (\RuntimeException $e) {
            $output->writeln("Waiting for InfluxDB... to push Nest data");
        }

        try {
            $this->pushOpenWeatherData($db);
            $output->writeln("Pushed Open Weather Data to InfluxDB");
        } catch (\RuntimeException $e) {
            $output->writeln("Waiting for InfluxDB... to push Open Weather data");
        }


    }

    public function pushNestData($db)
    {
        define('USERNAME', $this->getApplication()->getSilexApplication()['config']['nest']['username']);
        define('PASSWORD', $this->getApplication()->getSilexApplication()['config']['nest']['password']);

        $nest = new \Nest();
        $info = $nest->getDeviceInfo();
        $temperature = $info->current_state->temperature;
        $humidity = $info->current_state->humidity;
        $state = $info->target->mode;

        $db->insert("temperature", [ 'fields' => array('value' => $temperature)]);
        $db->insert("humidity", [ 'fields' => array('value' => $humidity)]);
        $db->insert("state", [ 'fields' => array('value' => $state)]);
    }

    public function pushOpenWeatherData($db)
    {
        $owm = new OpenWeatherMap();
        $weather = $owm->getWeather($this->getApplication()->getSilexApplication()['config']['openweather']['city_id'], 'metric', 'en');
        $temperature = $weather->temperature->now->getValue();
        $humidity = $weather->humidity->getValue();
        $pressure = $weather->pressure->getValue();
        $wind = $weather->wind->speed->getValue();
        $clouds = $weather->clouds->getValue();
        $precipitation = $weather->precipitation->getValue();

        $db->insert("outside.temperature", [ 'fields' => array('value' => $temperature)]);
        $db->insert("outside.humidity", [ 'fields' => array('value' => $humidity)]);
        $db->insert("outside.pressure", [ 'fields' => array('value' => $pressure)]);
        $db->insert("outside.wind", [ 'fields' => array('value' => $wind)]);
        $db->insert("outside.clouds", [ 'fields' => array('value' => $clouds)]);
        $db->insert("outside.precipitation", [ 'fields' => array('value' => $precipitation)]);
    }
}
