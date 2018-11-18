<?php

namespace App\Controller;

use App\Entity\Apartment;
use App\Entity\Upload;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApartmentController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;
    
    /** @var ObjectManager */
    private $em;

    /**
     * ApartmentController constructor.
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }

    /**
     * @Route(
     *     "/apartments",
     *     name="create_apartment",
     *     methods={"POST"},
     * )
     * @param Request $request
     * @return Response
     */
    public function createApartment(Request $request)
    {
        $body = $request->getContent();
        $apartment = $this->serializer->deserialize($body, Apartment::class, 'json');
        $this->em->persist($apartment);
        $this->em->flush();

        return new Response(
            $this->serializer->serialize(
                $apartment,
                'json',
                SerializationContext::create()->setGroups('basic')
            )
        );
    }

    /**
     * @Route(
     *     "/apartments/{apartmentId}/uploads/{uploadId}",
     *     name="create_apartment_upload",
     *     methods={"POST"},
     *     requirements={"apartmentId"="\d+", "uploadId"="\d+"},
     * )
     * @param int $apartmentId
     * @param int $uploadId
     * @return Response
     */
    public function createApartmentUpload(int $apartmentId, int $uploadId)
    {
        $apartment = $this->em->getRepository(Apartment::class)->find($apartmentId);
        $upload = $this->em->getRepository(Upload::class)->find($uploadId);

        $apartment->addUpload($upload);
        $this->em->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "/apartments/{apartmentId}",
     *     name="delete_apartment",
     *     methods={"DELETE"},
     *     requirements={"apartmentId"="\d+"},
     * )
     * @param $apartmentId
     * @return Response
     */
    public function deleteApartment($apartmentId)
    {
        $apartment = $this->em->getRepository(Apartment::class)->find($apartmentId);
        $this->em->remove($apartment);
        $this->em->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "/apartments/{apartmentId}/uploads/{uploadId}",
     *     name="delete_apartment_upload",
     *     methods={"DELETE"},
     *     requirements={"apartmentId"="\d+", "uploadId"="\d+"},
     * )
     * @param int $apartmentId
     * @param int $uploadId
     * @return Response
     */
    public function deleteApartmentUpload(int $apartmentId, int $uploadId)
    {
        $apartment = $this->em->getRepository(Apartment::class)->find($apartmentId);
        $upload = $this->em->getRepository(Upload::class)->find($uploadId);
        $apartment->removeUpload($upload);
        $this->em->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "/apartments/{apartmentId}",
     *     name="get_apartment",
     *     methods={"GET"},
     *     requirements={"apartmentId"="\d+"},
     * )
     * @param $apartmentId
     * @return Response
     */
    public function getApartment($apartmentId)
    {
        $apartment = $this->em->getRepository(Apartment::class)->find($apartmentId);

        return new Response($this->serializer->serialize(
            $apartment,
            'json',
            SerializationContext::create()->setGroups(['basic'])
        ));
    }

    /**
     * @Route(
     *     "/apartments",
     *     name="get_apartments",
     *     methods={"GET"},
     * )
     * @return Response
     */
    public function getApartments()
    {
        $apartment = $this->em->getRepository(Apartment::class)->findAll();

        return new Response($this->serializer->serialize(
            $apartment,
            'json',
            SerializationContext::create()->setGroups(['basic'])
        ));
    }

    /**
     * @Route(
     *     "/apartments/{apartmentId}/uploads",
     *     name="get_apartment_uploads",
     *     methods={"GET"},
     *     requirements={"apartmentId"="\d+"},
     * )
     * @param $apartmentId
     * @return Response
     */
    public function getApartmentUploads(int $apartmentId)
    {
        $apartment = $this->em->getRepository(Apartment::class)->find($apartmentId);

        return new Response(
            $this->serializer->serialize(
                $apartment->getUploads(),
                'json',
                SerializationContext::create()
                    ->setGroups('basic')
                    ->setSerializeNull(true)
            )
        );
    }

    /**
     * @Route(
     *     "/apartments/{apartmentId}",
     *     name="update_apartment",
     *     methods={"PUT"},
     *     requirements={"apartmentId"="\d+"},
     * )
     * @param Request $request
     * @param $apartmentId
     * @return Response
     */
    public function updateApartment(Request $request, $apartmentId)
    {
        $apartment = $this->em->getRepository(Apartment::class)->find($apartmentId);
        $body = $request->getContent();
        /** @var Apartment $apartmentFromBody */
        $apartmentFromBody = $this->serializer->deserialize($body, Apartment::class, 'json');
        $apartment->setAddress($apartmentFromBody->getAddress());
        $apartment->setName($apartmentFromBody->getName());
        $apartment->setLegacyId($apartmentFromBody->getLegacyId());
        $this->em->flush();

        return new Response(
            $this->serializer->serialize(
                $apartment,
                'json',
                SerializationContext::create()->setGroups('basic')
            )
        );
    }
}
