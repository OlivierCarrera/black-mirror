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
        public function getShowData()
        {
            $resources = $this->getNetflixContent()->resources;

            $showData = [
                'officialSite' => $resources->officialSite,
                'name' => $resources->name,
                'genres' => implode(', ', $resources->genres),
                'status' => $resources->status,
                'rating' => $resources->rating->average,
                'image' => $resources->image->medium,
                'summary' => strip_tags($resources->summary)
            ];

            return $showData;
        }

        /*
         * Retrieve main data about the episodes
         *
         * @return array
         */
        public function getEpisodesData()
        {
            $episodes = $this->getNetflixContent()->resources->_embedded->episodes;

            $episodesData = [];
            foreach ($episodes as $episode) {
                $episodesData[$episode->season][$episode->number] = [
                    'url' => $this->container->get('router')->generate(
                        'episode',
                        ['season' => $episode->season, 'number' => $episode->number]
                    ),
                    'name' => $episode->name
                ];
            }

            return $episodesData;
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