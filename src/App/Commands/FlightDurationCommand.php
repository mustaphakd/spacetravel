<?php
namespace Console\App\Commands;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class FlightDurationCommand extends Command
{
    protected function configure()
    {
        $this->setName('flight-duration')
            ->setDescription('calculate number of days for shuttle to reach its target speed!')
            ->setHelp('commands requires initial speed S, growth rate X, multiplicative effect on initial speed N ')
            ->addOption('speed','S', InputOption::VALUE_REQUIRED, 'Provide initial traveling speed')
            ->addOption('rate','X', InputOption::VALUE_REQUIRED, 'Provide increase rate')
            ->addOption('factor','N', InputOption::VALUE_REQUIRED, 'Provide number to multiply initial speed by to reach target speed');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $speed = $input->getOption('speed');
        $rate = $input->getOption('rate');
        $factor = $input->getOption('factor');

        if( ($this->is_validTwoDecimalsFloat($speed, $output) == false) || ($this->is_validTwoDecimalsFloat($rate, $output) == false))
        {
            $output->writeln("<error> both [-S] speed: {$speed} and [-X] rate: {$rate} must be positive real number with 2 decimals</error>");
            return 1;
        }

        
        $factorInt = intval($factor);

        if ( !is_int($factorInt) || $factorInt <= 0 )
        {
            $output->writeln("<error> [-N] factor: {$factor} must be greater than 0</error>");
            return 1;
        }
        
        //$speedFloat = floatval($speed);
        $rateFloat = floatval($rate);

        // (speed * N ) / speed  => N
        // also -S 2000 -X 6.8 -N 10  gives a result of Y = 35.000215452968 thus ceil must be used
        $duration = ceil( log($factorInt) / log(1 + ($rateFloat / 100)) );

        $output->writeln("Y = {$duration}");

        return 0;
    }

    private function is_validTwoDecimalsFloat($number, OutputInterface $output)
    {
       if (!filter_var($number, FILTER_VALIDATE_FLOAT) && $number > 0) 
       {
           $output->writeln("<error> number: ${number} could not be filtered as float</error>");
           return false;
       }

       if(preg_match('/^[0-9]+(\.[0-9]{0,2})?$/', $number))
         return true;
       else
       {
           $output->writeln("<error> number: ${number} failed regex validation.</error>");
           return false;
       }
    }
}