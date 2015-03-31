<?php
namespace AppBundle\Services;

use Goutte\Client;
use AppBundle\Entity\Torrent;
use AppBundle\Entity\Film;

class CrawlerService {
    
    public function __construct($doctrine){
        $this->client = new Client();
        
        $this->client->getClient()->setDefaultOption('config/curl/' . CURLOPT_SSL_VERIFYPEER, false);
        
        $this->doctrine = $doctrine;
        $this->manager = $this->doctrine->getManager();
        $this->torrentRepo = $doctrine->getRepository("AppBundle:Torrent");
        $this->filmRepo = $doctrine->getRepository("AppBundle:Film");
    }
    
    protected function saveFilm($imdbId){
        $imdbCrawler = $this->client->request('GET', "http://www.imdb.com/title/tt" . $imdbId);

        $newFilm = new Film();

        $newFilm->setImdbId($imdbId);

        // Title
        $title = $imdbCrawler->filter('[itemprop="name"]');
        if( count( $title ) > 0 ) {
            $newFilm->setTitle( $title->first()->text() );
        }

        // DATE
        $date = $imdbCrawler->filter('meta[itemprop="datePublished"]');
        if(count($date)>0){
            $date = $date->first()->text();
        }

        // DIRECTOR
        $director = $imdbCrawler->filter('[itemprop="director"] [itemprop="name"]');
        if( count( $director ) > 0 ){
            $newFilm->setDirector($director->first()->text());
        }

        // THUMB
        $thumb = $imdbCrawler->filter('[itemprop="image"]');
        if (count($thumb) > 0) {
            $thumbSrc = $thumb->first()->attr("src");
            $newFilm->setThumbnail($thumbSrc);
        }

        // SCORE
        $rating = $imdbCrawler->filter('[itemprop="ratingValue"]');
        if( count( $rating ) > 0 ){
            $newFilm->setRating( $rating->first()->text() );
        }

        // VOTES
        $votes = $imdbCrawler->filter('[itemprop="ratingCount"]');
        if( count( $votes ) > 0 ){
            $newFilm->setVotes($votes->first()->text());
        }
        
        return $newFilm;
    }
    
    protected function saveTorrent($crawler, $magnetLink, $hash){
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
        }
        
        return $newTorrent;
    }
    
    public function crawlMeSomeGoodOlTorrents()
    {
        $kickassHomepageCrawler = $this->client->request('GET', 'http://kickass.to/movies/?field=seeders&sorder=desc');
        
        $kickassHomepageCrawler->filter('a.cellMainLink')->reduce(function($node, $i) {
            return $i < 10;
        })->each(function ($node) {

            // RECUPERE LE LIEN ABSOLU VERS LA PAGE "DETAIL DU TORRENT"
            $link = $node->selectLink($node->text())->link();
            
            // RECUPERE LE CONTENU DE LA PAGE DETAIL TORRENT
            $crawler = $this->client->request('GET', $link->getUri());
            
            // MAGNET LINK URL
            $magnetLink = $crawler->filter('.magnetlinkButton')->first()->attr('href');
            
            // TORRENT HASH
            $matches = array();
            $hash = preg_match("/btih:([0-9a-zA-Z]*)/", $magnetLink, $matches);
            
            $torrentAlreadyExists = $this->torrentRepo->findOneByHash($matches[1]);

            $imdbId = $crawler->filter('a[href*="http://www.imdb.com/title/tt"]');
            if (!$torrentAlreadyExists && count($imdbId) > 0 ) {
                $imdbId = $imdbId->first()->text();

                // BERK
                if( $this->filmRepo->findOneByImdbId($imdbId) ){
                    $film = $this->filmRepo->findOneByImdbId($imdbId);
                } else {
                    $film = $this->saveFilm($imdbId);
                }
                
                $newTorrent = $this->saveTorrent($crawler, $magnetLink, $hash);
                $newTorrent->setFilm($film);

                $this->manager->persist($newTorrent);
                $this->manager->persist($film);
            }
        });
        $this->manager->flush();
    }
}