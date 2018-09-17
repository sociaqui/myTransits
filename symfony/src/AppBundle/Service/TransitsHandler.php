<?php

namespace AppBundle\Service;

/**
 * A service that can handle most transits related logic
 */
class TransitsHandler
{
    /**
     * Basic validation of the request content
     * (is it a properly formatted json?, does it contain at least 2 locations?)
     * returns an array with the data already decoded
     * or false if the request doesn't pass validation
     * optionally, if an error parameter is passed by reference
     * it will retain information on why the request did not pass validation
     *
     * @param $request
     * @param string $error
     * @return bool|array
     */
    public function isValid($request, &$error = '')
    {
        // Check if request content is declared as json
        $type = $request->getContentType();
        if ($type != 'json') {
            $error = 'Wrong content format';
            return false;
        }

        // Get the content and try to decode it
        $content = $request->getContent();
        $data = json_decode($content, true);

        // Check if content decoded properly and contains at least 2 locations
        if (is_null($data)) {
            $error = json_last_error_msg();
            return false;
        } elseif (!isset($data['locations']) || count($data['locations']) < 2) {
            $error = 'Request had bad syntax or the parameters supplied were invalid';
            return false;
        } else {
            return $data;
        }
    }

    /**
     * Makes an API call on Map Quest to further validate the data from the request
     * and calculate the optimal route and distance
     * returns an array with a full set of data
     * or false if there are any problems with the Map Quest API
     * optionally, if an error parameter is passed by reference
     * it will retain information on any encountered problems
     *
     * @param array $data
     * @param string $error
     * @return bool|array
     */
    public function enhanceData($data, &$error = '')
    {
        // Get the full endpoint URL
        $endpoint = 'http://www.mapquestapi.com/directions/v2/optimizedroute';
        $queryTemplate = '?key=%s&json=%s';
        $apiKey = '8K2ObuNwayMmCSvBAEkk77W9oxzhjz5g';
        $options = array(
            "unit" => "K",
            "routeType" => "shortest",
            "narrativeType" => "none",
        );
        $jsonArray = array(
            'options' => $options,
            'locations' => $data['locations'],
        );
        $json = urlencode(json_encode($jsonArray));
        $query = sprintf($queryTemplate, $apiKey, $json);
        $url = $endpoint . $query;

        // Setup cURL
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FAILONERROR => TRUE,
        ));

        // Send the request
        $rawResponse = curl_exec($ch);

        // Check for errors
        if ($rawResponse === FALSE) {
            $error = [curl_getinfo($ch, CURLINFO_HTTP_CODE),
                curl_getinfo($ch),
                curl_error($ch),
            ];
            return false;
        } else {
            // Decode the response
            $mapQuestData = json_decode($rawResponse, TRUE);
        }

        // Enhance original request data with data from MapQuest
        $enhancedData = array(
            'id' => $mapQuestData['route']['sessionId'],
            'distanceKilometers' => $mapQuestData['route']['distance'],
        );
        $enhancedData['locations'] = array_replace($mapQuestData['route']['locationSequence'],$data['locations']);

        return $enhancedData;
    }
}