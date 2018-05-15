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
use App\Models\Trello\Card;
use App\Services\Trello\TrelloClientInterface;
use Psr\Log\LoggerInterface;
use Underscore\Types\Strings;

class TrelloTrackerService implements TrackerServiceInterface
{

    private const BUG = 'Bug';
    private const FEATURE = 'Feature';
    private const LIKE = 'Like';

    /** @var \Twig_Environment */
    private $twigEnv;

    /** @var \Swift_Mailer */
    private $mailer;

    /** @var LoggerInterface */
    private $logger;

    /** @var TrelloClientInterface */
    private $trelloClient;

    /**
     * TrelloTrackerService constructor.
     * @param \Twig_Environment $twigEnv
     * @param \Swift_Mailer $mailer
     * @param LoggerInterface $logger
     * @param TrelloClientInterface $trelloClient
     */
    public function __construct(\Twig_Environment $twigEnv, \Swift_Mailer $mailer, LoggerInterface $logger, TrelloClientInterface $trelloClient)
    {
        $this->twigEnv = $twigEnv;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->trelloClient = $trelloClient;
    }

    /**
     * Submits the given bug
     *
     * @param Bug $bug
     * @return Submission
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function submitBug(Bug $bug): Submission
    {
        $desc = $this->twigEnv->load('@Cards\bug.twig')->render([
            'bug' => $bug->toArray()
        ]);
        $guessedLabels = $this->guessLabels($bug->getTitle(), $bug->getDetails(), $bug->getUrl());
        $guessedLabels[] = $this->trelloClient->getLabelId('Bug');

        $card = $this->trelloClient->submitCard(Card::fromArray([
            'name' => $bug->getTitle(),
            'desc' => $desc,
            'labels' => $guessedLabels
        ]));

        $this->trelloClient->attachFile($card['id'], 'phpinfo.html', $bug->getPhpInfo());
        $link = $card['shortUrl'];
        $this->sendInformationToDevelopers($bug, self::BUG, $bug->getWho() . ' found a bug', $link);

        return new Submission($link);
    }

    /**
     * Submits the given feature request
     *
     * @param Feature $featureRequest
     * @return Submission
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function submitFeature(Feature $featureRequest): Submission
    {
        $desc = $this->twigEnv->load('@Cards\feature.twig')->render([
            'feature' => $featureRequest->toArray()
        ]);

        $guessedLabels = $this->guessLabels($featureRequest->getTitle(), $featureRequest->getDetails());
        $guessedLabels[] = $this->trelloClient->getLabelId('Feature');

        $card = $this->trelloClient->submitCard(Card::fromArray([
            'name' => $featureRequest->getTitle(),
            'desc' => $desc,
            'labels' => $guessedLabels
        ]));
        $link = $card['shortUrl'];

        $this->sendInformationToDevelopers($featureRequest, self::FEATURE, $featureRequest->getWho() . ' found a bug', $link);

        return new Submission($link);
    }

    /**
     * Submits the given like
     *
     * @param Like $like
     * @return Submission
     */
    public function submitLike(Like $like): Submission
    {
        $this->sendInformationToDevelopers($like, self::LIKE, $like->getWho() . ' likes Jinya');

        return new Submission('');
    }

    /**
     * @param Bug|Feature|Like $data
     * @param string $type
     * @param string $subject
     * @param string $link
     */
    private function sendInformationToDevelopers($data, string $type, string $subject, string $link = ''): void
    {
        try {
            $typeToLower = strtolower($type);
            $template = $this->twigEnv->load("@Mail/$typeToLower.twig");
            $developersName = getenv('DEVELOPERS_NAME');
            $developersMail = getenv('DEVELOPERS_MAIL');
            $botName = getenv('BOT_NAME');
            $botMail = getenv('BOT_MAIL');

            /** @var \Swift_Message $message */
            $message = $this->mailer->createMessage();
            $message->addTo($developersMail)
                ->addFrom($botMail, $botName)
                ->setBody($template->render([
                    'who' => $data->getWho(),
                    'link' => $link,
                    'message' => $data instanceof Like ? $data->getMessage() : '',
                    'botName' => $botName,
                    'developersName' => $developersName
                ]), 'text/html', 'UTF-8')
                ->setSubject($subject);

            $this->mailer->send($message);
        } catch (\Throwable $e) {
            $this->logger->warning("Couldn't send info message: " . $e->getMessage());
            $this->logger->warning($e->getTraceAsString());
        }
    }

    /**
     * Tries to guess the labels based on the content
     *
     * @param $title
     * @param $details
     * @param $url
     * @return array|string[]
     */
    private function guessLabels(string $title, string $details, string $url = ''): array
    {
        $labels = [];

        $data = $title . "\n" . $details . "\n" . $url;
        if (!empty($url) && Strings::find(Strings::lower($url), 'designer')) {
            $labels[] = 'Designer';
        } else if (!empty($url)) {
            $labels[] = 'Frontend';
        }

        if (preg_match('/(backend|designer)/', $data)) {
            $labels[] = 'Designer';
        }
        if (preg_match('/(security|login|token)/', $data)) {
            $labels[] = 'Security';
        }
        if (preg_match('/(database)/', $data)) {
            $labels[] = 'Generic/Data';
        }

        $guessedLabels = array_values(array_map(function (string $label) {
            return $this->trelloClient->getLabelId($label);
        }, $labels));

        return $guessedLabels;
    }
}