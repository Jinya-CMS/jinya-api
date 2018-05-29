<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 17:40
 */

namespace App\Framework;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class BaseApiController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * BaseApiController constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Executes the given @see callable and return a formatted error if it fails
     *
     * @param callable $function
     * @param int $successStatusCode
     * @return array
     */
    protected function tryExecute(callable $function, int $successStatusCode = Response::HTTP_OK)
    {
        try {
            return [$function(), $successStatusCode];
        } /* @noinspection PhpRedundantCatchClauseInspection */ catch (FileNotFoundException $exception) {
            return [$this->jsonFormatException('The requested url is not available', $exception), Response::HTTP_NOT_FOUND];
        } catch (Throwable $throwable) {
            $this->logger->error($throwable->getMessage());
            $this->logger->error($throwable->getTraceAsString());

            return [$this->jsonFormatException('Internal server error', $throwable), Response::HTTP_INTERNAL_SERVER_ERROR];
        }
    }

    /**
     * Formats the given @see Throwable as array
     *
     * @param string $message
     * @param Throwable $throwable
     * @return array
     */
    protected function jsonFormatException(string $message, Throwable $throwable): array
    {
        $data = [
            'success' => false,
            'error' => [
                'message' => $message,
            ],
        ];
        if ($this->isDebug()) {
            $data['error']['exception'] = $throwable->getMessage();
            $data['error']['file'] = $throwable->getFile();
            $data['error']['stack'] = $throwable->getTraceAsString();
            $data['error']['line'] = $throwable->getLine();
        }

        return $data;
    }

    /**
     * Checks if we are currently in a debugging environment
     *
     * @return bool
     */
    private function isDebug(): bool
    {
        $env = $_SERVER['APP_ENV'] ?? 'dev';

        return $_SERVER['APP_DEBUG'] ?? ('prod' !== $env);
    }
}
