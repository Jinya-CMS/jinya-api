<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 27.07.18
 * Time: 21:14
 */

namespace App\Services\Tracker;


use App\Models\Tracker\Bug;
use App\Models\Tracker\Feature;
use App\Models\Tracker\Like;
use App\Models\Tracker\Submission;
use Psr\Log\LoggerInterface;

class EmailTrackerService implements TrackerServiceInterface
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

    /**
     * EmailTrackerService constructor.
     * @param \Twig_Environment $twigEnv
     * @param \Swift_Mailer $mailer
     * @param LoggerInterface $logger
     */
    public function __construct(\Twig_Environment $twigEnv, \Swift_Mailer $mailer, LoggerInterface $logger)
    {
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
        $this->sendTicketMail($bug, self::BUG, $bug->getTitle());

        return new Submission('');
    }

    /**
     * @param Bug|Feature $data
     * @param string $type
     * @param string $subject
     */
    private function sendTicketMail($data, string $type, string $subject): void
    {
        try {
            $typeToLower = strtolower($type);
            $template = $this->twigEnv->load("@Cards/$typeToLower.twig");
            $supportMail = getenv('SUPPORT_MAIL');
            $botName = getenv('BOT_NAME');
            $botMail = getenv('BOT_MAIL');

            $templateData = $data->toArray();
            $templateData['botName'] = $botName;

            /** @var \Swift_Message $message */
            $message = $this->mailer->createMessage();
            $message->addTo($supportMail)
                ->addFrom($botMail, $botName)
                ->setBody($template->render($templateData), 'text/html', 'UTF-8')
                ->setSubject($subject);

            if ($data instanceof Bug) {
                $phpInfo = new \Swift_Attachment($data->getPhpInfo(), 'phpinfo.html', 'text/html');
                $message->attach($phpInfo);
            }

            $this->mailer->send($message);
        } catch (\Throwable $e) {
            $this->logger->warning("Couldn't send ticket message: " . $e->getMessage());
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
        $this->sendTicketMail($featureRequest, self::FEATURE, $featureRequest->getTitle());

        return new Submission('');
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
                    'developersName' => $developersName,
                ]), 'text/html', 'UTF-8')
                ->setSubject($subject);

            $this->mailer->send($message);
        } catch (\Throwable $e) {
            $this->logger->warning("Couldn't send info message: " . $e->getMessage());
            $this->logger->warning($e->getTraceAsString());
        }
    }
}