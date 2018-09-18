<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Transit;
use AppBundle\Service\TransitsHandler;

class TransitsController extends Controller
{
    /**
     * List all transits in the DB.
     *
     * @Route("/transits", name="list_transits", methods={"GET"})
     * @return Response
     */
    public function showAllAction(Request $request)
    {
        // initialize repository
        $repository = $this->getDoctrine()->getRepository(Transit::class);

        // look for multiple Product objects matching the name, ordered by price
        $collection = $repository->findBy(
            [],
            ['createdAt' => 'DESC']
        );

        //serialize the Transit object and send it in the response
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($collection, 'json');
        $eTag = md5($jsonContent);

        $response = new JsonResponse();
        $response->setCache([
            'public' => true,
            'etag' => $eTag,
        ]);

        if ($response->isNotModified($request)) {
            $response->setStatusCode(304);
        } else {
            $response->setJson($jsonContent);
            $response->setEncodingOptions(JSON_PRETTY_PRINT);
        }

        return $response;
    }

    /**
     * Validate data provided, enhance it with data from Map Quest, create a new Transit Entity with it,
     * save it to the DB and display the result.
     *
     * @Route("/transits", name="add_transit", methods={"POST"})
     * @param Request $request
     * @param TransitsHandler $handler
     * @return Response
     */
    public function addNewAction(Request $request, TransitsHandler $handler)
    {
        $error = '';

        // validate the data provided
        $data = $handler->isValid($request, $error);

        if ($data === false) {
            return new Response($error, '400');
        }

        //resend the data to Map Quest API for additional validation, optimal route generation, and distance calculation
        $enhancedData = $handler->enhanceData($data, $error);

        if ($enhancedData === false) {
            return new Response($error, '400');
        }

        $entityManager = $this->getDoctrine()->getManager();

        try {
            $transit = new Transit($enhancedData);
            $entityManager->persist($transit);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new Response($e, '400');
        }

        //serialize the Transit object and send it in the response
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($transit, 'json');

        $response = new Response();
        $response->setContent($jsonContent);
        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }
}
