<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Goutte\Client;
use AppBundle\Entity\Torrent;
use AppBundle\Entity\Film;

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

    protected function getLinks() {
        
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $client = new Client();
        $client->getCLient()->setDefaultOption('config/curl/' . CURLOPT_SSL_VERIFYPEER, false);
        $crawler = $client->request('GET', 'http://kickass.to/movies/?field=seeders&sorder=desc');

        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');
        $manager = $doctrine->getManager();
        $torrentRepo = $doctrine->getRepository("AppBundle:Torrent");
        $filmRepo = $doctrine->getRepository("AppBundle:Film");

        $crawler->filter('a.cellMainLink')->reduce(function($node, $i) {
            return $i < 20;
        })->each(function ($node) use ($client, $torrentRepo, $filmRepo, $manager) {

            // RECUPERE LE LIEN VERS LA PAGE "DETAIL DU TORRENT"
            $link = $node->selectLink($node->text())->link();
            $crawler = $client->request('GET', $link->getUri());
            $magnetLink = $crawler->filter('.magnetlinkButton')->first()->attr('href');
            $hash = preg_match("/btih:([0-9a-zA-Z]*)/", $magnetLink, $matches);
            $hash = $matches[1];
            $torrentAlreadyExists = $torrentRepo->findOneByHash($hash);

            if (!$torrentAlreadyExists) {

                $newTorrent = new Torrent();

                // TITLE
                $torrentTitle = $crawler->filter('.novertmarg')->first();
                $newTorrent->setTitle($torrentTitle->text());

                // MAGNET
                $newTorrent->setMagnet($magnetLink);

                // HASH
                $newTorrent->setHash($hash);

                // SEEDERS
                $seeders = $crawler->filter('[itemprop="seeders"]')->first()->text();
                $newTorrent->setSeeders($seeders);

                // LEECHERS
                $leechers = $crawler->filter('[itemprop="leechers"]')->first()->text();
                $newTorrent->setLeechers($leechers);

                //QUALITY
                $quality = $crawler->filter('[id^="quality"]');
                if (count($quality) > 0) {
                    $newTorrent->setQuality($quality->first()->text());
                };

                $manager->persist($newTorrent);

                //IMDB
                $imdbId = $crawler->filter('a[href*="http://www.imdb.com/title/tt"]');
                if (count($imdbId) > 0) {
                    $imdbId = $imdbId->first()->text();
                    $film = $filmRepo->findOneByImdbId($imdbId);
                    if ($film) {
                        $newTorrent->setFilm($film);
                    } else {
                        $crawler = $client->request('GET', "http://www.imdb.com/title/tt" . $imdbId);

                        $newFilm = new Film();

                        $newFilm->setImdbId($imdbId);

                        // Title
                        $title = $crawler->filter('[itemprop="name"]');
                        if( count( $title ) > 0 ) {
                            $newFilm->setTitle( $title->first()->text() );
                        }
                        
                        // DATE
                        $date = $crawler->filter('[itemprop="datePublished"]');
                        if(count($date)>0){
                            $date = $date->first()->text();
                        }

                        // DIRECTOR
                        $director = $crawler->filter('[itemprop="director"] [itemprop="name"]');
                        if( count( $director ) > 0 ){
                            $newFilm->setDirector($director->first()->text());
                        }
                        

                        // THUMB
                        $thumb = $crawler->filter('[itemprop="image"]');
                        if (count($thumb) > 0) {
                            $thumbSrc = $thumb->first()->attr("src");
                            $newFilm->setThumbnail($thumbSrc);
                        }

                        // SCORE
                        $rating = $crawler->filter('[itemprop="ratingValue"]');
                        if( count( $rating ) > 0 ){
                            $newFilm->setRating( $rating->first()->text() );
                        }
                        
                        // VOTES
                        $votes = $crawler->filter('[itemprop="ratingCount"]');
                        if( count( $votes ) > 0 ){
                            $newFilm->setVotes($votes->first()->text());
                        }
                        
                        $newTorrent->setFilm($newFilm);

                        $manager->persist($newFilm);
                    }
                };
            }
        });
        $manager->flush();
    }

}
