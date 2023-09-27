<?php
/**
 * User: ingvar.aasen
 * Date: 05.06.2023
 */

namespace Iaasen\Matrikkel\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractCommand extends Command {
	protected SymfonyStyle $io;

	public function initialize(InputInterface $input, OutputInterface $output) : void {
		$this->io = new SymfonyStyle($input, $output);
		parent::initialize($input, $output);
	}


	public function iterate(array $objects) : void {
		$getTheRest = false;
		$current = 0;
		$count = count($objects);

		foreach($objects AS $object) {
			$current++;
			dump($object);
			$this->io->text("<info>Entry $current/$count</info>");
			if(!$getTheRest && $current < $count) {
				$answer = $this->io->ask("Get next ('a' to get all, 'q' to break)");
				if($answer == 'a') $getTheRest = true;
				if($answer == 'q') break;
			}
		}
	}


	/**
	 * @param array|string|null $withString
	 * @return array
	 */
	public static function extractWith($withString) : array {
		if(is_null($withString)) return [];
		if(is_array($withString)) return $withString;
		if(preg_match('/([{\[])/', $withString) >= 1) {
			$with = json_decode($withString, 1);
			if(is_null($with)) throw new InvalidArgumentException("Invalid format of 'with' attribute (is this correct json?)");
			return (array) $with;
		}
		else return explode(',', $withString);
	}


	protected function printOptions(int $limit, array $with, array $withOptions, array $searchFields) : void {
		$this->io->horizontalTable(
			[
				'Limit (-l)',
				'With (-w)',
				'Available with options',
				'Available search fields',
			],
			[
				[
					$limit,
					json_encode($with),
					json_encode($withOptions),
					implode(', ', $searchFields),
				],
			]
		);
	}

}