<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 14.05.18
 * Time: 22:08
 */

namespace App\Services\Tracker;


use App\Models\Tracker\Bug;
use App\Models\Tracker\Feature;
use App\Models\Tracker\Like;
use App\Models\Tracker\Submission;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class TrelloTrackerService implements TrackerServiceInterface
{

    private const BUG = 'Bug';
    private const FEATURE = 'Feature';
    private const LIKE = 'Like';

    /** @var Client */
    private $guzzleClient;

    /** @var \Twig_Environment */
    private $twigEnv;

    /** @var \Swift_Mailer */
    private $mailer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * TrelloTrackerService constructor.
     * @param Client $guzzleClient
     * @param \Twig_Environment $twigEnv
     * @param \Swift_Mailer $mailer
     * @param LoggerInterface $logger
     */
    public function __construct(Client $guzzleClient, \Twig_Environment $twigEnv, \Swift_Mailer $mailer, LoggerInterface $logger)
    {
        $this->guzzleClient = $guzzleClient;
        $this->twigEnv = $twigEnv;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * Submits the given bug
     *
     * @param Bug $bug
     * @return Submission
     */
    public function submitBug(Bug $bug): Submission
    {
        $link = null;
        $this->sendInformationToDevelopers($bug, self::BUG, $link);

        return new Submission();
    }

    /**
     * @param Bug|Feature|Like $data
     * @param string $type
     * @param string $link
     */
    private function sendInformationToDevelopers($data, string $type, string $link = ''): void
    {
        try {
            $template = $this->twigEnv->load(__DIR__ . "/Templates/${type}Template.twig");
            /** @var \Swift_Message $message */
            $message = $this->mailer->createMessage();
            $message
                ->addTo('developers@jinya.de')
                ->addFrom('trello@jinya.de', 'Jinya Trello Bot')
                ->setBody($template->render([
                    'who' => $data->getWho(),
                    'link' => $link,
                    'message' => $data instanceof Like ? $data->getMessage() : ''
                ]), 'text/html', 'UTF-8')
                ->setSubject('Someone likes Jinya');

            $this->mailer->send($message);
        } catch (\Throwable $e) {
            $this->logger->warning("Couldn't send info message: " . $e->getMessage());
            $this->logger->warning($e->getTraceAsString());
        }
    }

    /**
     * Submits the given feature request
     *
     * @param Feature $featureRequest
     * @return Submission
     */
    public function submitFeature(Feature $featureRequest): Submission
    {
        $link = null;
        $this->sendInformationToDevelopers($featureRequest, self::FEATURE, $link);

        return new Submission();
    }

    /**
     * Submits the given like
     *
     * @param Like $like
     * @return Submission
     */
    public function submitLike(Like $like): Submission
    {
        $this->sendInformationToDevelopers($like, self::LIKE);

        return new Submission();
    }
}