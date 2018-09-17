<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Service\TransitsHandler;

class TransitsController extends Controller
{
    /**
     * @Route("/transits", name="list_transits", methods={"GET"})
     * @return Response
     */
    public function showAllAction()
    {
        // list all transits in DB
        return new Response(
            '<html><body>GET entrypoint</body></html>'
        );
    }

    /**
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

        //resend the data to Map Quest API for aditional validation, optimal route generation, and distance calculation
        $enhancedData = $handler->enhanceData($data, $error);

        if ($enhancedData === false) {
            return new Response($error, '400');
        }

        // if all goes well, create a new Transit entity and save to DB

        return new Response(json_encode($enhancedData));
    }
}
