<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of CrawlCommand
 *
 * @author Theo
 */
class CrawlCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('pirate:crawl')
                ->setDescription('Crawl kickass.to for new content to download!');
        
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $crawler = $this->getContainer()->get('crawler');
        
        $crawler->crawlMeSomeGoodOlTorrents();
    }

}
