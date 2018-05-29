<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 14.05.18
 * Time: 21:54
 */

namespace App\Models\Tracker;

class Like
{
    /** @var string */
    private $who;

    /** @var string */
    private $message;

    /**
     * Creates a like from the given array
     *
     * @param array $array
     * @return Like
     */
    public static function fromArray(array $array): self
    {
        $like = new self();
        $like->message = array_key_exists('message', $array) ? $array['message'] : null;
        $like->who = $array['who'];

        return $like;
    }

    /**
     * @return string
     */
    public function getWho(): string
    {
        return $this->who;
    }

    /**
     * @param string $who
     */
    public function setWho(string $who): void
    {
        $this->who = $who;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
