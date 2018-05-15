<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 14.05.18
 * Time: 21:46
 */

namespace App\Controller\Tracker;


use App\Framework\BaseApiController;
use App\Models\Tracker\Bug;
use App\Models\Tracker\Feature;
use App\Models\Tracker\Like;
use App\Services\Tracker\TrackerServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrackerController extends BaseApiController
{
    /**
     * @Route("/api/tracker/bug", name="api_tracker_bug", methods={"POST"})
     *
     * @param Request $request
     * @param TrackerServiceInterface $trackerService
     * @return Response
     */
    public function bugAction(Request $request, TrackerServiceInterface $trackerService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $trackerService) {
            return $trackerService->submitBug(Bug::fromArray(json_decode($request->getContent(), true)));
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/tracker/feature", name="api_tracker_feature", methods={"POST"})
     *
     * @param Request $request
     * @param TrackerServiceInterface $trackerService
     * @return Response
     */
    public function featureRequestAction(Request $request, TrackerServiceInterface $trackerService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $trackerService) {
            return $trackerService->submitFeature(Feature::fromArray(json_decode($request->getContent(), true)));
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/tracker/like", name="api_tracker_like", methods={"POST"})
     *
     * @param Request $request
     * @param TrackerServiceInterface $trackerService
     * @return Response
     */
    public function likeAction(Request $request, TrackerServiceInterface $trackerService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $trackerService) {
            return $trackerService->submitLike(Like::fromArray(json_decode($request->getContent(), true)));
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

}