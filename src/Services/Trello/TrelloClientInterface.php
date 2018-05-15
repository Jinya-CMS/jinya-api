<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 15.05.18
 * Time: 15:02
 */

namespace App\Services\Trello;


use App\Models\Trello\Card;

interface TrelloClientInterface
{
    /**
     * Submits the given card to trello
     *
     * @param Card $card
     * @return array
     */
    public function submitCard(Card $card): array;

    /**
     * Attaches the given file to the given card
     *
     * @param string $cardId
     * @param string $name
     * @param string $data
     * @param string $mimeType
     */
    public function attachFile(string $cardId, string $name, string $data, string $mimeType = 'text/html'): void;

    /**
     * Gets the label id for the given name
     *
     * @param string $name
     * @return string
     */
    public function getLabelId(string $name): ?string;
}