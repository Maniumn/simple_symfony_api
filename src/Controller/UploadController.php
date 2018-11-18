<?php

namespace App\Controller;

use App\Entity\Upload;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{
    public const FILE_FORM_NAME = 'uploads';

    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    /**
     * @Route(
     *     "/uploads/{uploadId}",
     *     name="delete_upload",
     *     methods={"DELETE"},
     *     requirements={"uploadId"="\d+"},
     * )
     * @param $uploadId
     * @return Response
     */

    public function deleteUpload(int $uploadId)
    {
        $upload = $this->em->getRepository(Upload::class)->find($uploadId);

        if (!is_null($upload)) {
            $this->em->remove($upload);
            $this->em->flush();
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Does not save the file
     *
     * @Route(
     *     "/uploads",
     *     name="create_upload",
     *     methods={"POST"},
     * )
     * @return Response
     */
    public function createUpload(Request $request)
    {
        $uploadedFiles = [];

        if ($request->files->get(self::FILE_FORM_NAME)) {
            $uploadFiles = $request->files->get(self::FILE_FORM_NAME);
            $uploadFiles = (is_array($uploadFiles)) ? $uploadFiles : [$uploadFiles];

            /** @var UploadedFile $uploadFile */
            foreach ($uploadFiles as $uploadFile) {
                $internalFileName = uniqid('f_') .'.'.$uploadFile->getClientOriginalExtension();
                $upload = new Upload();
                $upload->setInternalFileName($internalFileName);
                $upload->setOriginalFileName($uploadFile->getClientOriginalName());

                $uploadedFiles[] = $upload;

                $this->em->persist($upload);
            }

            $this->em->flush();
        }

        return new Response(
            $this->serializer->serialize(
                $uploadedFiles,
                'json',
                SerializationContext::create()
                    ->setGroups('basic')
                    ->setSerializeNull(true)
            )
        );
    }

    /**
     * @Route(
     *     "/uploads/{uploadId}",
     *     name="update_upload_title",
     *     methods={"PUT"},
     *     requirements={"uploadId"="\d+"},
     * )
     * @param Request $request
     * @param int $uploadId
     * @return Response
     */
    public function updateUploadTitle(Request $request, int $uploadId)
    {
        $json = json_decode($request->getContent(), true);
        $title = $json['title'];
        $upload = $this->em->getRepository(Upload::class)->find($uploadId);

        $upload->setTitle($title);

        $this->em->flush();

        return new Response(
            $this->serializer->serialize(
                $upload,
                'json',
                SerializationContext::create()
                    ->setGroups('basic')
                    ->setSerializeNull(true)
            )
        );
    }
}
