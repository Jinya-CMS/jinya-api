<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 14.05.18
 * Time: 21:46
 */

namespace App\Services\Tracker;

use App\Models\Tracker\Bug;
use App\Models\Tracker\Feature;
use App\Models\Tracker\Like;
use App\Models\Tracker\Submission;

interface TrackerServiceInterface
{
    /**
     * Submits the given bug
     *
     * @param Bug $bug
     * @return Submission
     */
    public function submitBug(Bug $bug): Submission;

    /**
     * Submits the given feature request
     *
     * @param Feature $featureRequest
     * @return Submission
     */
    public function submitFeature(Feature $featureRequest): Submission;

    /**
     * Submits the given like
     *
     * @param Like $like
     * @return Submission
     */
    public function submitLike(Like $like): Submission;
}
