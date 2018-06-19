<?php
    // src/AppBundle/Service/NetflixApi.php
    namespace AppBundle\Service;

    use Symfony\Component\DependencyInjection\ContainerInterface as Container;

    class NetflixApi
    {
        private $data = null;

        private $container;

        public function __construct(Container $container) {
            $this->container = $container;
        }

        /*
         * Retrieve data about the show
         *
         * @return array
         */
        public function getShow()
        {
            $resources = $this->getNetflixContent()->resources;

            $show = [
                'officialSite' => $resources->officialSite,
                'name' => $resources->name,
                'genres' => implode(', ', $resources->genres),
                'status' => $resources->status,
                'rating' => $resources->rating->average,
                'image' => $resources->image->medium,
                'summary' => strip_tags($resources->summary)
            ];

            return $show;
        }

        /*
         * Retrieve main data about the episodes
         *
         * @return array
         */
        public function getEpisodes()
        {
            $data = $this->getNetflixContent()->resources->_embedded->episodes;

            $episodes = [];
            foreach ($data as $episode) {
                $episodes[$episode->season][$episode->number] = [
                    'url' => $this->container->get('router')->generate(
                        'episode',
                        ['season' => $episode->season, 'number' => $episode->number]
                    ),
                    'name' => $episode->name
                ];
            }

            return $episodes;
        }

        /*
         * Retrieve all data about the episode
         *
         * @param int $season
         * @param int $number
         * @return array
         */
        public function getEpisode($season, $number)
        {
            $episodes = $this->getNetflixContent()->resources->_embedded->episodes;

            $data = [];
            foreach ($episodes as $episode) {
                if ($episode->season == $season && $episode->number == $number) {
                    $date = date("d/m/Y", strtotime($episode->airdate));
                    $data = [
                        'name' => $episode->name,
                        'url' => $episode->url,
                        'season' => $season,
                        'number' => $number,
                        'airdate' => $date,
                        'image' => $episode->image->medium,
                        'summary' => strip_tags($episode->summary)
                    ];
                }
            }

            return $data;
        }

        /*
         * Retrieve data from API
         *
         * @return stdClass
         */
        public function getNetflixContent()
        {
            if ($this->data == null) {
                $return = null;

                try {
                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, 'http://adneomapisubject.herokuapp.com/blackmirror');
                    curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, 'GET');
                    curl_setopt($cURL, CURLOPT_HTTPHEADER, array('X-Auth-Token: TokenADNTest2018'));
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_HEADER, false);

                    $return = json_decode(curl_exec($cURL));
                } catch (\Exception $e) {

                }

                // Call backup file if no data returned, or update it
                $backupDir = $this->container->getParameter('kernel.root_dir') . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'backup';
                $backupFile = 'netflix.json';
                if ($return == null || $return->code != '200') {
                    $return = json_decode(file_get_contents($backupDir . DIRECTORY_SEPARATOR . $backupFile));
                } else {
                    if (!is_dir($backupDir)) {
                        mkdir($backupDir);
                    }
                    file_put_contents($backupDir . DIRECTORY_SEPARATOR . $backupFile, $return);
                }

                $this->data = $return;
            }

            return $this->data;
        }
    }