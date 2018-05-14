<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Point;
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
        $client = new Client("influxdb", 8086, "root", "root");
        $db = $client->selectDB("nest");

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

    public function pushNestData(Database $db)
    {
        define('USERNAME', $this->getApplication()->getSilexApplication()['config']['nest']['username']);
        define('PASSWORD', $this->getApplication()->getSilexApplication()['config']['nest']['password']);

        $nest = new \Nest();
        $info = $nest->getDeviceInfo();
        $temperature = $info->current_state->temperature;
        $humidity = $info->current_state->humidity;
        $state = $info->target->mode;

        $points = [
          new Point("temperature", $temperature),
          new Point("humidity", $humidity),
          new Point("state", $state)
        ];
        $this->sendPoints($db, $points);
    }

    public function pushOpenWeatherData(Database $db)
    {
        $owm = new OpenWeatherMap();
        $config = $this->getApplication()->getSilexApplication()['config']['openweather'];
        $weather = $owm->getWeather($config['city_id'], 'metric', 'en', $config['app_id']);
        $temperature = $weather->temperature->now->getValue();
        $humidity = $weather->humidity->getValue();
        $pressure = $weather->pressure->getValue();
        $wind = $weather->wind->speed->getValue();
        $clouds = $weather->clouds->getValue();
        $precipitation = $weather->precipitation->getValue();

        $points = [
          new Point("outside.temperature", $temperature),
          new Point("outside.humidity", $humidity),
          new Point("outside.pressure", $pressure),
          new Point("outside.wind", $wind),
          new Point("outside.clouds", $clouds),
          new Point("outside.precipitation", $precipitation)
        ];
        $this->sendPoints($db, $points);
    }

    private function sendPoints(Database $db, array $points)
    {
      $db->writePoints($points, Database::PRECISION_MINUTES);
    }
}
