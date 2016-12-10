<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest; 

use Doctrine\ORM\Tools\Pagination\Paginator;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\PlaceType;
use AppBundle\Entity\Place;

use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;


class PlaceController extends Controller
{

    /**
     * @ApiDoc(description="Get liste of place from application",
     *    output= { "class"=Place::class })
     *
     * @Rest\View()
     * @Rest\Get("/places")     
     * 
     */
    public function getPlacesAction(Request $request)
    {
        $limit = $request->query->getInt('limit', 10);
        $page = $request->query->getInt('page', 1);
        $sorting = $request->query->get('sorting', array());

     
        $em = $this->get('doctrine.orm.entity_manager');
        $placesPager = $em
                ->getRepository('AppBundle:Place')
                ->findAllPaginated($limit, $page, $sorting);

        $pagerFactory = new PagerfantaFactory();
        
        return $pagerFactory->createRepresentation(
                    $placesPager,
                    new Route('get_places', array(
                        'limit' => $limit,
                        'page' => $page,
                        'sorting' => $sorting
                    ))
                );

    }

    /**
     * @ApiDoc(description="Get One place from application",
     *    output= { "class"=Place::class, "collection"=true, "groups"={"place"} })
     * @Rest\View(serializerGroups={"place"})
     * @Rest\Get("/places/{id}")
     */
    public function getPlaceAction(Request $request)
    {
        $place = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Place')
                ->find($request->get('id')); 

        /* @var $place Place */

        if (empty($place)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        return $place;
    }

    /**
     * @ApiDoc(description="Create a new place",
     *      input={ "class"=PlaceType::class},
     *      output= { "class"=Place::class, "collection"=true, "groups"={"place"} })
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"place"})
     * @Rest\Post("/places")
     */
    public function postPlacesAction(Request $request)
    {
        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);

        $form->submit($request->request->all()); 

        if($form->isSubmitted() && $form->isValid()){        
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($place);
            $em->flush();
            return $place;
        } else {
            return $form;
        }
    }

    /**
     * @ApiDoc(description="Update all informations of one place",
     *    output= { "class"=Place::class, "collection"=true, "groups"={"place"} })
     * @Rest\View(serializerGroups={"place"})
     * @Rest\Put("/places/{id}")
     */
    public function updatePlaceAction(Request $request)
    {
        return $this->updatePlace($request, true);
    }

    /**
     * @ApiDoc(description="Create a part of one place",
     *    output= { "class"=Place::class, "collection"=true, "groups"={"place"} })
     * @Rest\View(serializerGroups={"place"})
     * @Rest\Patch("/places/{id}")
     */
    public function patchPlaceAction(Request $request)
    {
        return $this->updatePlace($request, false);
    }

    private function updatePlace(Request $request, $clearMissing)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $place = $em
                ->getRepository('AppBundle:Place')
                ->find($request->get('id')); 
        /* @var $place Place */

        if (empty($place)) {
            return \FOS\RestBundle\View\View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PlaceType::class, $place);
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em->persist($place);
            $em->flush();
            return $place;
        } else {
            return $form;
        }
    }

    /**
     * @ApiDoc(description="Delete one place from application",
     *    output= { "class"=Place::class, "collection"=true, "groups"={"place"} })
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT,
     *            serializerGroups={"place"})
     * @Rest\Delete("/places/{id}")
     */
    public function removePlaceAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $place = $em->getRepository("AppBundle:Place")
                    ->find($request->get('id'));
        /* @var $place Place */

        if ($place) {
            foreach ($place->getPrices() as $price) {
                $em->remove($price);
            }
            $em->remove($place);
            $em->flush();
        }
    }
}