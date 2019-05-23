<?php

namespace App\Controller;

use App\Entity\UserQualification;
use App\Formatter\UserQualificationFormatter;
use App\Repository\UserQualificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/v{version}/qualifications", requirements={"version": "\d+"})
 */
class UserQualificationController extends BaseController
{
    /** @var UserQualificationRepository */
    protected $repository;

    /** @var UserQualificationFormatter */
    protected $formatter;

    /**
     * UserQualificationController constructor.
     *
     * @param UserQualificationRepository $repository
     * @param UserQualificationFormatter  $formatter
     */
    public function __construct(UserQualificationRepository $repository, UserQualificationFormatter $formatter)
    {
        $this->repository = $repository;
        $this->formatter  = $formatter;
    }

    /**
     * @Route(path="", methods={"GET"})
     *
     * @return Response
     */
    public function listQualifications(): Response
    {
        $qualifications = $this->repository->getUserQualifications();

        $qualificationsToReturn = [];
        foreach ($qualifications as $qualification) {
            $qualificationsToReturn[] = $this->formatter->format($qualification);
        }

        return JsonResponse::create([
            'qualifications' => $qualificationsToReturn,
        ]);
    }

    /**
     * @Route(path="", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createQualification(Request $request): Response
    {
        $qualification = new UserQualification();
        $qualification->setTitle($request->request->get('title'));

        $this->repository->save($qualification);

        return JsonResponse::create($this->formatter->format($qualification));
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, requirements={"id": "\d+"})
     *
     * @param UserQualification $qualification
     *
     * @return Response
     */
    public function fetchQualification(UserQualification $qualification): Response
    {
        return JsonResponse::create($this->formatter->format($qualification));
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request           $request
     * @param UserQualification $qualification
     *
     * @return Response
     */
    public function editQualification(Request $request, UserQualification $qualification): Response
    {
        $qualification->setTitle($request->request->get('title'));

        $this->repository->save($qualification);

        return JsonResponse::create($this->formatter->format($qualification));
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"}, requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param UserQualification $qualification
     *
     * @return Response
     */
    public function removeQualification(UserQualification $qualification): Response
    {
        $this->repository->delete($qualification);

        return JsonResponse::create([]);
    }
}
