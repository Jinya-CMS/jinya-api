<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 15.05.18
 * Time: 15:02
 */

namespace App\Services\Trello;


use App\Models\Trello\Card;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Underscore\Types\Arrays;

class TrelloClient implements TrelloClientInterface
{

    /** @var Client */
    private $client;

    /**
     * TrelloClient constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Submits the given card to trello
     *
     * @param Card $card
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function submitCard(Card $card): array
    {
        $queryParameters = $card->jsonSerialize();
        $queryParameters['key'] = $this->apiKey();
        $queryParameters['token'] = $this->oauthToken();

        $uri = 'https://api.trello.com/1/cards';
        $response = $this->client->request('POST', $uri, [
            RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
            RequestOptions::JSON => $queryParameters
        ]);
        $newCard = json_decode($response->getBody()->getContents(), true);

        return $newCard;
    }

    private function apiKey()
    {
        return getenv('TRELLO_API_KEY');
    }

    private function oauthToken()
    {
        return getenv('TRELLO_OAUTH_TOKEN');
    }

    /**
     * Gets the label id for the given name
     *
     * @param string $name
     * @return string
     */
    public function getLabelId(string $name): ?string
    {
        $response = $this->client->get(sprintf('https://api.trello.com/1/boards/%s/labels/?fields=name', $this->boardId()));
        $labels = json_decode($response->getBody()->getContents(), true);

        $label = array_filter($labels, function ($item) use ($name) {
            return strtolower($item['name']) === strtolower($name);
        });

        if (empty($label)) {
            return null;
        } else {
            return Arrays::first($label)['id'];
        }
    }

    private function boardId()
    {
        return getenv('TRELLO_BOARD_ID');
    }

    /**
     * Attaches the given file to the given card
     *
     * @param string $cardId
     * @param string $name
     * @param string $data
     * @param string $mimeType
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function attachFile(string $cardId, string $name, string $data, string $mimeType = 'text/html'): void
    {
        $uri = "https://api.trello.com/1/cards/$cardId/attachments";

        $this->client->request('POST', $uri, [
            'multipart' => [
                [
                    'name' => 'key',
                    'contents' => $this->apiKey()
                ],
                [
                    'name' => 'token',
                    'contents' => $this->oauthToken()
                ],
                [
                    'name' => 'mimeType',
                    'contents' => $mimeType
                ],
                [
                    'name' => 'name',
                    'contents' => $name
                ],
                [
                    'name' => 'file',
                    'contents' => $data,
                    'filename' => $name
                ]
            ]
        ]);
    }
}