<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Goutte\Client;

/**
 * Description of CrawlCommand
 *
 * @author Theo
 */
class CrawlCommand extends ContainerAwareCommand{
    protected function configure()
    {
        $this
            ->setName('pirate:crawl')
            ->setDescription('Crawl kickass.to for new content to download!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = $output;
        $client = new Client();
        $crawler = $client->request('GET', 'http://kickass.to/movies/?field=seeders&sorder=desc');
        $crawler->filter('a.cellMainLink')->each(function ($node, $output) {
            print($node->text()."\n");
        });
    }
}
